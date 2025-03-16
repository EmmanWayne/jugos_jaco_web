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
                
                // Limpiar completamente el mapa
                clearMap();
                
                // Limpiar búsquedas y resultados
                document.getElementById('clientSearch').value = '';
                document.getElementById('employeeSearch').value = '';
                document.getElementById('clientSearchResults').classList.add('hidden');
                document.getElementById('employeeSearchResults').classList.add('hidden');
                
                // Actualizar UI
                document.getElementById('clientFilters').classList.toggle('hidden', mode !== 'clients');
                document.getElementById('employeeFilters').classList.toggle('hidden', mode !== 'employees');
                document.getElementById('clientModeBtn').classList.toggle('active', mode === 'clients');
                document.getElementById('employeeModeBtn').classList.toggle('active', mode === 'employees');
                
                // Mostrar datos según el modo
                if (mode === 'clients') {
                    showAllClients();
                } else {
                    showAllEmployees();
                }
            }

            function clearMap() {
                // Limpiar todos los marcadores existentes
                if (currentMarkers.length > 0) {
                    currentMarkers.forEach(marker => marker.remove());
                    currentMarkers = [];
                }
                
                // Limpiar todas las rutas
                if (currentRoute) {
                    currentRoute.remove();
                    currentRoute = null;
                }
                
                // Limpiar marcadores de clientes
                Object.values(clientMarkers).forEach(marker => marker.remove());
                clientMarkers = {};
                
                // Limpiar rutas de empleados
                Object.values(employeeRoutes).forEach(route => {
                    if (route.route) route.route.remove();
                    if (route.marker) route.marker.remove();
                });
                employeeRoutes = {};
            }

            function showAllClients() {
                const clients = @json($clients);
                bounds = L.latLngBounds();
                let lastLocation = null;
                
                clients.forEach(client => {
                    if (client.has_location) {
                        const marker = addClientMarker(client);
                        bounds.extend([client.location.lat, client.location.lng]);
                        
                        // Guardar la ubicación más reciente
                        if (!lastLocation || client.location.updated_at > lastLocation.updated_at) {
                            lastLocation = client.location;
                        }
                    }
                });

                if (lastLocation) {
                    // Centrar en la última ubicación con un zoom razonable
                    map.setView([lastLocation.lat, lastLocation.lng], 13);
                } else if (!bounds.isEmpty()) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                updateClientStats(clients);
            }

            function showAllEmployees() {
                const employees = @json($employeeLocations);
                bounds = L.latLngBounds();
                let lastLocation = null;
                let lastUpdateTime = null;
                
                employees.forEach((employee, index) => {
                    if (employee.locations && employee.locations.length > 0) {
                        const routeColor = routeColors[index % routeColors.length];
                        const routePoints = employee.locations.map(loc => [loc.lat, loc.lng]);
                        
                        // Agregar ruta
                        const route = L.polyline(routePoints, {
                            color: routeColor,
                            weight: 3,
                            opacity: 0.7
                        }).addTo(map);

                        // Agregar marcador
                        const marker = L.marker([employee.locations[0].lat, employee.locations[0].lng], {
                            icon: L.divIcon({
                                className: 'custom-employee-marker',
                                html: `
                                    <div class="employee-label px-3 py-1.5 rounded-full shadow-md text-white"
                                         style="background-color: ${routeColor}; border: 2px solid white;">
                                        <span class="font-medium">${employee.nombre}</span>
                                        ${employee.en_ruta ? '<span class="ml-1 text-xs">●</span>' : ''}
                                    </div>
                                `
                            })
                        }).addTo(map);

                        employeeRoutes[employee.id] = { route, marker };
                        routePoints.forEach(point => bounds.extend(point));

                        // Verificar si esta es la ubicación más reciente
                        const currentUpdateTime = new Date(employee.locations[0].timestamp);
                        if (!lastUpdateTime || currentUpdateTime > lastUpdateTime) {
                            lastUpdateTime = currentUpdateTime;
                            lastLocation = employee.locations[0];
                        }
                    }
                });

                if (lastLocation) {
                    // Centrar en la última ubicación con un zoom razonable
                    map.setView([lastLocation.lat, lastLocation.lng], 13);
                } else if (!bounds.isEmpty()) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                updateEmployeeStats(employees);
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
                                       class="bg-[#4285F4] text-white px-3 py-1 rounded-md text-sm hover:bg-[#3367D6] transition-colors duration-200 flex items-center">
                                        <svg class="w-10 h-10 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                        Ver en Maps
                                    </a>
                                    <a href="${client.location.whatsapp_url}" 
                                       target="_blank"
                                       class="bg-[#25D366] text-white px-3 py-1 rounded-md text-sm hover:bg-[#128C7E] transition-colors duration-200 flex items-center">
                                        <svg class="w-10 h-10 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-3.825 3.113-6.937 6.937-6.937 1.856.001 3.598.723 4.907 2.034 1.31 1.311 2.031 3.054 2.03 4.908-.001 3.825-3.113 6.938-6.937 6.938z"/>
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
                        html: `<div class="w-8 h-auto flex items-center justify-center text-white rounded-full" 
                              style="background-color: ${color}">
                                ${employee.nombre.charAt(0)}
                              </div>`
                    })
                }).addTo(map);

                employeeRoutes[employee.id] = { route, marker };
                routePoints.forEach(point => bounds.extend(point));
            }

            // Iniciar en modo clientes
            switchMode('clients');
        });
    </script>
    @endpush
</x-filament::page>