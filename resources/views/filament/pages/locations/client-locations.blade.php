<x-filament::page>
    <div class="space-y-4">
        {{-- Filtros --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Buscador de clientes --}}
            <div class="p-4 bg-white rounded-lg shadow relative z-50">
                <label for="clientSearch" class="block text-sm font-medium text-gray-700">Buscar Cliente</label>
                <select id="clientSearch" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Todos los clientes</option>
                </select>
                <div id="locationStatus" class="mt-2 text-sm text-gray-500 hidden"></div>
            </div>

            {{-- Filtro de empleados --}}
            <div class="p-4 bg-white rounded-lg shadow relative z-50">
                <label for="employeeFilter" class="block text-sm font-medium text-gray-700">Filtrar por Empleado</label>
                <select id="employeeFilter" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Todos los empleados</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee['id'] }}">{{ $employee['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Lista de Clientes --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Clientes</h3>
            <div class="overflow-y-auto max-h-48 space-y-2" id="clientsList">
                {{-- La lista se llenará dinámicamente con JavaScript --}}
            </div>
        </div>

        {{-- Mapa --}}
        <div class="bg-white rounded-lg shadow relative z-0">
            <div id="map" style="height: 700px; width: 100%; border-radius: 0.5rem;"></div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .leaflet-popup-content {
                margin: 8px;
            }
            .client-popup {
                min-width: 200px;
                text-align: center;
            }
            /* Asegurar que los dropdowns de TomSelect aparezcan sobre el mapa */
            .ts-dropdown {
                z-index: 1000 !important;
            }
            /* Asegurar que el contenedor de TomSelect esté sobre el mapa */
            .ts-wrapper {
                position: relative;
                z-index: 100;
            }
            .client-card {
                padding: 1rem;
                border-radius: 0.5rem;
                border: 1px solid #e5e7eb;
                margin-bottom: 0.5rem;
            }
            .client-card:hover {
                background-color: #f3f4f6;
            }
            .client-card.selected {
                background-color: #e5e7eb;
                border-color: #d1d5db;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const clients = @json($clients);
                const center = @json($center);
                let map, markers = {};
                const ITEMS_PER_PAGE = 5;
                let currentPage = 0;

                // Inicializar mapa
                initMap();
                initFilters();
                updateClientsList();

                function initMap() {
                    map = L.map('map').setView([center.lat, center.lng], 7);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    // Agregar marcadores solo para clientes con ubicación
                    clients.forEach(client => {
                        if (client.has_location) {
                            addMarker(client);
                        }
                    });
                }

                function updateClientsList(filteredClients = null) {
                    const clientsList = document.getElementById('clientsList');
                    const displayClients = filteredClients || clients;
                    clientsList.innerHTML = '';

                    const start = currentPage * ITEMS_PER_PAGE;
                    const end = start + ITEMS_PER_PAGE;
                    const pageClients = displayClients.slice(start, end);

                    pageClients.forEach(client => {
                        const card = document.createElement('div');
                        card.className = 'client-card';
                        card.innerHTML = `
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">${client.nombre}</h4>
                                    <p class="text-sm text-gray-600">${client.direccion}</p>
                                    <p class="text-sm text-gray-600">Empleado: ${client.empleado}</p>
                                </div>
                                <div class="text-sm">
                                    ${client.has_location ? 
                                        `<span class="text-green-600">✓ Con ubicación</span>` : 
                                        `<span class="text-red-600">✗ Sin ubicación</span>`}
                                </div>
                            </div>
                        `;

                        if (client.has_location) {
                            card.onclick = () => {
                                map.setView([client.location.lat, client.location.lng], 15);
                                markers[client.id].openPopup();
                                document.querySelectorAll('.client-card').forEach(c => c.classList.remove('selected'));
                                card.classList.add('selected');
                            };
                        }

                        clientsList.appendChild(card);
                    });

                    // Agregar controles de paginación si hay más páginas
                    if (displayClients.length > ITEMS_PER_PAGE) {
                        const totalPages = Math.ceil(displayClients.length / ITEMS_PER_PAGE);
                        const pagination = document.createElement('div');
                        pagination.className = 'flex justify-between items-center mt-4';
                        pagination.innerHTML = `
                            <button class="px-3 py-1 text-sm bg-gray-100 rounded-md ${currentPage === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                                    ${currentPage === 0 ? 'disabled' : ''}>
                                Anterior
                            </button>
                            <span class="text-sm text-gray-600">
                                Página ${currentPage + 1} de ${totalPages}
                            </span>
                            <button class="px-3 py-1 text-sm bg-gray-100 rounded-md ${currentPage >= totalPages - 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                    ${currentPage >= totalPages - 1 ? 'disabled' : ''}>
                                Siguiente
                            </button>
                        `;

                        const [prevBtn, nextBtn] = pagination.querySelectorAll('button');
                        prevBtn.onclick = () => {
                            if (currentPage > 0) {
                                currentPage--;
                                updateClientsList(displayClients);
                            }
                        };
                        nextBtn.onclick = () => {
                            if (currentPage < totalPages - 1) {
                                currentPage++;
                                updateClientsList(displayClients);
                            }
                        };

                        clientsList.appendChild(pagination);
                    }
                }

                function initFilters() {
                    // Inicializar búsqueda de clientes
                    const clientSelect = new TomSelect('#clientSearch', {
                        valueField: 'id',
                        labelField: 'nombre',
                        searchField: ['nombre', 'direccion'],
                        options: clients,
                        render: {
                            option: function(item, escape) {
                                return `<div>
                                    <div class="font-medium">${escape(item.nombre)}</div>
                                    <div class="text-sm text-gray-500">${escape(item.direccion)}</div>
                                    <div class="text-sm text-gray-500">Empleado: ${escape(item.empleado)}</div>
                                </div>`;
                            }
                        },
                        onChange: function(value) {
                            const statusDiv = document.getElementById('locationStatus');
                            if (value) {
                                const selectedClient = clients.find(c => c.id == value);
                                if (!selectedClient.has_location) {
                                    statusDiv.textContent = '⚠️ Este cliente no tiene ubicación registrada';
                                    statusDiv.classList.remove('hidden');
                                } else {
                                    statusDiv.classList.add('hidden');
                                }
                            } else {
                                statusDiv.classList.add('hidden');
                            }
                            filterMarkers();
                        }
                    });

                    // Inicializar filtro de empleados
                    const employeeSelect = new TomSelect('#employeeFilter', {
                        onChange: function(value) {
                            filterMarkers();
                        }
                    });

                    function filterMarkers() {
                        const selectedClient = clientSelect.getValue();
                        const selectedEmployee = employeeSelect.getValue();

                        // Filtrar clientes
                        const filteredClients = clients.filter(client => {
                            const matchesClient = !selectedClient || client.id == selectedClient;
                            const matchesEmployee = !selectedEmployee || client.employee_id == selectedEmployee;
                            return matchesClient && matchesEmployee;
                        });

                        // Actualizar lista de clientes
                        currentPage = 0; // Reset a la primera página
                        updateClientsList(filteredClients);

                        // Actualizar marcadores en el mapa
                        Object.values(markers).forEach(marker => marker.remove());
                        filteredClients.forEach(client => {
                            if (client.has_location) {
                                const marker = markers[client.id];
                                marker.addTo(map);
                                if (selectedClient && client.id == selectedClient) {
                                    map.setView([client.location.lat, client.location.lng], 15);
                                    marker.openPopup();
                                }
                            }
                        });

                        if (!selectedClient && !selectedEmployee) {
                            map.setView([center.lat, center.lng], 7);
                        }
                    }
                }

                function addMarker(client) {
                    const marker = L.marker([client.location.lat, client.location.lng])
                        .bindPopup(`
                            <div class="client-popup">
                                <h3 class="font-medium">${client.nombre}</h3>
                                <p class="text-sm">${client.direccion}</p>
                                <p class="text-sm">Empleado: ${client.empleado}</p>
                                <div class="mt-2">
                                    <a href="${client.location.maps_url}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800">Ver en Google Maps</a>
                                </div>
                                <div class="mt-1">
                                    <a href="${client.location.whatsapp_url}" target="_blank" 
                                       class="text-green-600 hover:text-green-800">Compartir por WhatsApp</a>
                                </div>
                            </div>
                        `);
                    
                    marker.addTo(map);
                    markers[client.id] = marker;
                }
            });
        </script>
    @endpush
</x-filament::page>
