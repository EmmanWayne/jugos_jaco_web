<x-filament::page>
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <style>
        .mode-button {
            @apply border-2 border-transparent transition-all duration-200;
        }
        .mode-button.active {
            @apply bg-primary-100 text-primary-700 border-primary-300;
        }
        #map {
            height: calc(100vh - 12rem);
            width: 100%;
            z-index: 1;
        }
        .employee-marker {
            background: white;
            border-radius: 50%;
            text-align: center;
        }
        .map-container {
            position: sticky;
            top: 2rem;
        }
        .search-result {
            @apply p-2 hover:bg-gray-100 cursor-pointer;
        }
    </style>
    @endpush

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Panel de Control (Izquierda) --}}
        <div class="space-y-4">
            {{-- Selector de modo --}}
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-center space-x-4">
                    <button id="clientModeBtn" class="mode-button active flex-1 px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center space-x-2">
                        <x-heroicon-s-users class="w-4 h-4" />
                        <span>Clientes</span>
                    </button>
                    <button id="employeeModeBtn" class="mode-button flex-1 px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center space-x-2">
                        <x-heroicon-s-map class="w-4 h-4" />
                        <span>Empleados</span>
                    </button>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="bg-white rounded-lg shadow p-4">
                <div id="clientFilters" class="space-y-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Buscar Cliente
                        </label>
                        <input type="text" 
                            id="clientSearch" 
                            class="w-full rounded-lg border-gray-300"
                            placeholder="Ingrese el nombre del cliente..."
                        />
                        <div id="clientSearchResults" class="hidden absolute z-50 w-full bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto mt-1">
                        </div>
                    </div>
                    
                    {{-- Estadísticas de Clientes --}}
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-500">Clientes con Ubicación</div>
                            <div class="text-lg font-semibold text-primary-600">
                                {{ $statistics['clients']['with_location'] }} / {{ $statistics['clients']['total'] }}
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-500">Sin Ubicación</div>
                            <div class="text-lg font-semibold text-gray-600" id="clientsWithoutLocation">0</div>
                        </div>
                    </div>

                    {{-- Lista de Clientes sin Ubicación --}}
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Clientes sin Ubicación</h3>
                        <div id="clientsWithoutLocationList" class="max-h-48 overflow-y-auto bg-gray-50 rounded-lg p-2">
                        </div>
                    </div>
                </div>

                <div id="employeeFilters" class="hidden space-y-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Buscar Empleado
                        </label>
                        <input type="text" 
                            id="employeeSearch" 
                            class="w-full rounded-lg border-gray-300"
                            placeholder="Ingrese el nombre del empleado..."
                        />
                        <div id="employeeSearchResults" class="hidden absolute z-50 w-full bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto mt-1">
                        </div>
                    </div>

                    {{-- Estadísticas de Empleados --}}
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-500">Total Empleados</div>
                            <div class="text-lg font-semibold text-primary-600">
                                {{ $statistics['employees']['total'] }}
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-500">Empleados Activos Hoy</div>
                            <div class="text-lg font-semibold text-primary-600">
                                {{ $statistics['employees']['active_today'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mapa (Derecha) --}}
        <div class="map-container">
            <div class="bg-white rounded-lg shadow">
                <div id="map"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar el mapa
            const map = L.map('map').setView([14.6349, -86.9315], 7);
            
            // Agregar capa base del mapa
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Variables globales
            let currentMode = 'clients';
            let currentMarkers = [];
            let currentRoute = null;
            let employeeRoutes = {};
            let clientMarkers = {};
            let bounds;

            // Colores para las rutas
            const routeColors = [
                '#3B82F6', '#EF4444', '#10B981', '#F59E0B', 
                '#6366F1', '#EC4899', '#8B5CF6', '#14B8A6'
            ];

            // Manejadores de eventos para los botones de modo
            document.getElementById('clientModeBtn').addEventListener('click', () => switchMode('clients'));
            document.getElementById('employeeModeBtn').addEventListener('click', () => switchMode('employees'));

            // Buscador de clientes mejorado
            const clientSearch = document.getElementById('clientSearch');
            const clientSearchResults = document.getElementById('clientSearchResults');

            function updateClientStats(clients) {
                const withLocation = clients.filter(c => c.has_location).length;
                const withoutLocation = clients.length - withLocation;
                
                document.getElementById('clientsWithLocation').textContent = withLocation;
                document.getElementById('clientsWithoutLocation').textContent = withoutLocation;

                // Actualizar lista de clientes sin ubicación
                const withoutLocationList = document.getElementById('clientsWithoutLocationList');
                withoutLocationList.innerHTML = clients
                    .filter(c => !c.has_location)
                    .map(client => `
                        <div class="text-sm p-2 hover:bg-gray-100 rounded">
                            ${client.nombre}
                            <div class="text-xs text-gray-500">${client.direccion}</div>
                        </div>
                    `).join('');
            }

            clientSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const clients = @json($clients);
                
                if (searchTerm.length < 2) {
                    clientSearchResults.classList.add('hidden');
                    showAllClients(); // Mostrar todos los clientes cuando no hay búsqueda
                    return;
                }

                const filteredClients = clients.filter(client => 
                    client.nombre.toLowerCase().includes(searchTerm)
                );

                clientSearchResults.innerHTML = filteredClients.map(client => `
                    <div class="search-result p-2 hover:bg-gray-100 cursor-pointer" data-client-id="${client.id}">
                        <div class="font-medium">${client.nombre}</div>
                        <div class="text-xs text-gray-500">${client.direccion}</div>
                        ${client.has_location ? 
                            '<span class="text-xs text-green-600">Con ubicación</span>' : 
                            '<span class="text-xs text-red-600">Sin ubicación</span>'}
                    </div>
                `).join('');

                clientSearchResults.classList.remove('hidden');
            });

            // Selección de cliente
            clientSearchResults.addEventListener('click', function(e) {
                const result = e.target.closest('.search-result');
                if (result) {
                    const clientId = result.dataset.clientId;
                    const clients = @json($clients);
                    const selectedClient = clients.find(c => c.id == clientId);

                    if (selectedClient && selectedClient.has_location) {
                        clearMap();
                        const marker = addClientMarker(selectedClient);
                        map.setView([selectedClient.location.lat, selectedClient.location.lng], 15);
                        marker.openPopup();
                    }

                    clientSearch.value = selectedClient.nombre;
                    clientSearchResults.classList.add('hidden');
                }
            });

            // Buscador de empleados mejorado
            const employeeSearch = document.getElementById('employeeSearch');
            const employeeSearchResults = document.getElementById('employeeSearchResults');

            function updateEmployeeStats(employees) {
                const total = employees.length;
                const withRoutes = employees.filter(e => e.locations && e.locations.length > 0).length;
                
                document.getElementById('totalEmployees').textContent = total;
                document.getElementById('employeesWithRoutes').textContent = withRoutes;
            }

            employeeSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const employees = @json($employeeLocations);
                
                if (searchTerm.length < 2) {
                    employeeSearchResults.classList.add('hidden');
                    showEmployeeRoutes(); // Mostrar todas las rutas cuando no hay búsqueda
                    return;
                }

                const filteredEmployees = employees.filter(employee => 
                    employee.nombre.toLowerCase().includes(searchTerm)
                );

                employeeSearchResults.innerHTML = filteredEmployees.map(employee => `
                    <div class="search-result p-2 hover:bg-gray-100 cursor-pointer" data-employee-id="${employee.id}">
                        <div class="font-medium">${employee.nombre}</div>
                        ${employee.locations.length > 0 ? 
                            `<span class="text-xs text-green-600">${employee.locations.length} puntos registrados</span>` : 
                            '<span class="text-xs text-red-600">Sin rutas registradas</span>'}
                    </div>
                `).join('');

                employeeSearchResults.classList.remove('hidden');
            });

            // Selección de empleado
            employeeSearchResults.addEventListener('click', function(e) {
                const result = e.target.closest('.search-result');
                if (result) {
                    const employeeId = result.dataset.employeeId;
                    const employee = @json($employeeLocations).find(e => e.id == employeeId);
                    
                    clearMap();
                    
                    if (employee.locations.length > 0) {
                        const routeColor = routeColors[0];
                        const routePoints = employee.locations.map(loc => [loc.lat, loc.lng]);
                        
                        // Dibujar la ruta
                        currentRoute = L.polyline(routePoints, {
                            color: routeColor,
                            weight: 3,
                            opacity: 0.7
                        }).addTo(map);

                        // Agregar marcador en la última ubicación
                        const lastLocation = employee.locations[0];
                        const marker = L.marker([lastLocation.lat, lastLocation.lng], {
                            icon: L.divIcon({
                                className: 'employee-marker',
                                html: `<div class="bg-white px-2 py-1 rounded-lg shadow text-sm border-2" 
                                       style="border-color: ${routeColor}">
                                        ${employee.nombre}
                                      </div>`
                            })
                        }).addTo(map);

                        marker.bindPopup(`
                            <div class="p-2">
                                <h3 class="font-bold">${employee.nombre}</h3>
                                <p class="text-sm">Última actualización: ${lastLocation.timestamp}</p>
                                <div class="mt-2">
                                    <a href="${lastLocation.maps_url}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        Ver en Google Maps
                                    </a>
                                </div>
                            </div>
                        `);

                        currentMarkers.push(marker);
                        
                        // Ajustar el mapa para mostrar toda la ruta
                        const bounds = L.latLngBounds(routePoints);
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }

                    employeeSearch.value = employee.nombre;
                    employeeSearchResults.classList.add('hidden');
                }
            });

            // Funciones principales
            function switchMode(mode) {
                currentMode = mode;
                clearMap();
                updateUI();
                if (mode === 'clients') {
                    showAllClients();
                } else {
                    showEmployeeRoutes();
                }
            }

            function showAllClients() {
                const clients = @json($clients);
                bounds = L.latLngBounds();
                clearMap();
                
                clients.forEach(client => {
                    if (client.has_location) {
                        const marker = addClientMarker(client);
                        bounds.extend([client.location.lat, client.location.lng]);
                    }
                });

                if (!bounds.isEmpty()) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                updateClientStats(clients);
            }

            function addClientMarker(client) {
                const marker = L.marker([client.location.lat, client.location.lng])
                    .bindPopup(`
                        <div class="p-3">
                            <h3 class="font-bold text-lg mb-2">${client.nombre}</h3>
                            <div class="space-y-2">
                                <p class="text-sm">
                                    <span class="font-medium">Dirección:</span><br>
                                    ${client.direccion}
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Departamento:</span><br>
                                    ${client.department}
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Municipio:</span><br>
                                    ${client.township}
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Teléfono:</span><br>
                                    ${client.phone_number}
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Empleado Asignado:</span><br>
                                    ${client.empleado}
                                </p>
                                <div class="mt-3 pt-2 border-t flex space-x-2">
                                    <a href="${client.location.maps_url}" 
                                       target="_blank"
                                       class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0C4.477 0 0 4.477 0 10c0 5.523 4.477 10 10 10s10-4.477 10-10c0-5.523-4.477-10-10-10zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm-1-13v6h2V5H9zm0 8v2h2v-2H9z"/>
                                        </svg>
                                        Ver en Maps
                                    </a>
                                    <a href="${client.location.whatsapp_url}" 
                                       target="_blank"
                                       class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0C4.477 0 0 4.477 0 10c0 5.523 4.477 10 10 10s10-4.477 10-10c0-5.523-4.477-10-10-10zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm-1-13v6h2V5H9zm0 8v2h2v-2H9z"/>
                                        </svg>
                                        Compartir por WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    `)
                    .addTo(map);
                
                clientMarkers[client.id] = marker;
                return marker;
            }

            function showEmployeeRoutes() {
                const employees = @json($employeeLocations);
                bounds = L.latLngBounds();
                clearMap();

                employees.forEach((employee, index) => {
                    if (employee.locations && employee.locations.length > 0) {
                        const routeColor = routeColors[index % routeColors.length];
                        addEmployeeRoute(employee, routeColor);
                    }
                });

                if (!bounds.isEmpty()) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                updateEmployeeStats(employees);
            }

            function addEmployeeRoute(employee, color) {
                const routePoints = employee.locations.map(loc => [loc.lat, loc.lng]);
                
                const route = L.polyline(routePoints, {
                    color: color,
                    weight: 3,
                    opacity: 0.7
                }).addTo(map);

                const lastLocation = employee.locations[0];
                const marker = L.marker([lastLocation.lat, lastLocation.lng], {
                    icon: L.divIcon({
                        className: 'employee-marker',
                        html: `<div class="w-8 h-8 flex items-center justify-center text-white rounded-full" 
                              style="background-color: ${color}">
                                ${employee.nombre.charAt(0)}
                              </div>`
                    })
                }).addTo(map);

                employeeRoutes[employee.id] = { route, marker };
                routePoints.forEach(point => bounds.extend(point));
            }

            function clearMap() {
                Object.values(clientMarkers).forEach(marker => marker.remove());
                Object.values(employeeRoutes).forEach(route => {
                    route.route.remove();
                    route.marker.remove();
                });
                clientMarkers = {};
                employeeRoutes = {};
                currentMarkers = [];
                if (currentRoute) {
                    currentRoute.remove();
                    currentRoute = null;
                }
            }

            function updateUI() {
                document.getElementById('clientFilters').classList.toggle('hidden', currentMode !== 'clients');
                document.getElementById('employeeFilters').classList.toggle('hidden', currentMode !== 'employees');
                
                document.getElementById('clientModeBtn').classList.toggle('active', currentMode === 'clients');
                document.getElementById('employeeModeBtn').classList.toggle('active', currentMode === 'employees');
            }

            

            // Inicializar mostrando todos los clientes
            showAllClients();
        });
    </script>
    @endpush
</x-filament::page>