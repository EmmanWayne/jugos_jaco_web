<x-filament::page>
    <div class="space-y-4">
        {{-- Filtros --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Buscador de clientes --}}
            <div class="p-4 bg-white rounded-lg shadow">
                <select id="clientSearch" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">Buscar cliente...</option>
                </select>
            </div>

            {{-- Filtro de empleados --}}
            <div class="p-4 bg-white rounded-lg shadow">
                <select id="employeeFilter" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">Todos los empleados</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee['id'] }}">{{ $employee['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Contenedor del mapa --}}
        <div id="map" style="height: 600px; width: 100%; border-radius: 0.5rem;"></div>

        {{-- Scripts en el layout --}}
        @push('scripts')
            {{-- Leaflet CSS --}}
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

            {{-- Tom Select CSS para el buscador --}}
            <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

            {{-- Estilos personalizados --}}
            <style>
                .leaflet-popup-content-wrapper {
                    border-radius: 8px;
                }

                .client-popup {
                    padding: 8px;
                    min-width: 200px;
                }

                .client-popup h3 {
                    font-size: 16px;
                    font-weight: 600;
                    color: #1a1a1a;
                    margin: 8px 0;
                    text-align: center;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 4px;
                }

                .client-popup p:first-child {
                    margin-top: 0;
                    font-weight: 500;
                }

                .client-popup p {
                    font-size: 14px;
                    color: #4b5563;
                    margin: 4px 0;
                    text-align: center;
                }

                .highlighted-marker {
                    animation: bounce 0.5s infinite alternate;
                }

                @keyframes bounce {
                    from {
                        transform: translateY(0);
                    }

                    to {
                        transform: translateY(-10px);
                    }
                }

                .share-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 8px;
                    margin-top: 12px;
                    padding-top: 8px;
                    border-top: 1px solid #eee;
                }

                .share-button {
                    padding: 6px 12px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                    color: white !important;
                }

                .whatsapp-button {
                    background-color: #25D366;
                    color: white !important;
                }

                .maps-button {
                    background-color: #4285F4;
                    color: white !important;
                }

                .employee-whatsapp-button {
                    background-color: #128C7E;
                    color: white;
                }

                .share-button:hover {
                    opacity: 0.9;
                    color: white !important;
                }
            </style>

            {{-- Leaflet y Tom Select JS --}}
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

            {{-- Inicialización del mapa y buscador --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Centro en Honduras
                    const honduras = [14.6349, -86.9315];

                    // Crear el mapa
                    const map = L.map('map').setView(honduras, 7);

                    // Agregar capa de Stadia Maps con fondo verde
                    L.tileLayer('https://tiles.stadiamaps.com/tiles/stamen_terrain/{z}/{x}/{y}{r}.jpg', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://stamen.com/">Stamen Design</a>',
                    }).addTo(map);

                    // Obtener los clientes del backend
                    const clients = @json($clients);
                    console.log('Datos de clientes:', clients); // Para debug

                    // Objeto para almacenar los marcadores por ID de cliente
                    const markersById = {};

                    // Crear un grupo de marcadores
                    const markers = L.featureGroup();

                    // Preparar opciones para el buscador
                    const searchOptions = clients.map(client => ({
                        value: client.id || `${client.latitude}-${client.longitude}`,
                        text: `${client.nombres} ${client.apellidos} - ${client.direccion}`
                    }));

                    // Agregar marcadores para cada cliente
                    clients.forEach(client => {
                        const fullName = `${client.nombres} ${client.apellidos}`;

                        // Popup simple para mouseover
                        const hoverPopupContent = `
                            <div class="client-popup">
                                <h3>${fullName}</h3>
                            </div>
                        `;

                        // Popup detallado para click
                        const clickPopupContent = `
                            <div class="client-popup">
                                <h3>${fullName}</h3>
                                <p>${client.direccion}</p>
                                <p style="color: #666; font-size: 12px;">${client.coordenadas}</p>
                                <div style="margin-top: 8px; text-align: center;">
                                    <p style="color: #4a5568; margin-bottom: 2px;">
                                        <strong>Empleado Asignado</strong>
                                    </p>
                                    <p style="color: #4a5568;">${client.empleado_asignado}</p>
                                </div>
                                <div class="share-buttons">
                                    <a href="${client.whatsapp_share}" target="_blank" class="share-button whatsapp-button">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                        </svg>
                                        Compartir
                                    </a>
                                    <a href="${client.maps_url}" target="_blank" class="share-button maps-button">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                        </svg>
                                        Ver en Maps
                                    </a>
                                </div>
                            </div>
                        `;

                        const marker = L.marker([
                            parseFloat(client.latitude),
                            parseFloat(client.longitude)
                        ]);

                        // Crear dos popups diferentes
                        const hoverPopup = L.popup({
                            closeButton: false,
                            closeOnClick: false,
                            autoClose: false,
                            className: 'custom-popup'
                        }).setContent(hoverPopupContent);

                        const clickPopup = L.popup({
                            closeButton: true,
                            closeOnClick: false,
                            autoClose: false,
                            className: 'custom-popup'
                        }).setContent(clickPopupContent);

                        // Mostrar popup simple al pasar el mouse
                        marker.on('mouseover', function(e) {
                            if (!marker.isPopupOpen()) {
                                marker.bindPopup(hoverPopup).openPopup();
                            }
                        });

                        // Mostrar popup detallado al hacer click
                        marker.on('click', function(e) {
                            marker.unbindPopup();
                            marker.bindPopup(clickPopup).openPopup();
                        });

                        markers.addLayer(marker);
                        markersById[client.id || `${client.latitude}-${client.longitude}`] = marker;
                    });

                    markers.addTo(map);

                    // Ajustar el zoom inicial
                    if (clients.length > 0) {
                        map.fitBounds(markers.getBounds(), {
                            padding: [50, 50]
                        });
                    }

                    // Inicializar el buscador
                    new TomSelect('#clientSearch', {
                        options: searchOptions,
                        placeholder: 'Buscar cliente...',
                        onChange: function(value) {
                            if (!value) return;

                            // Remover resaltado de todos los marcadores
                            Object.values(markersById).forEach(marker => {
                                marker.getElement()?.classList.remove('highlighted-marker');
                            });

                            const selectedMarker = markersById[value];
                            if (selectedMarker) {
                                // Centrar el mapa en el marcador seleccionado
                                map.setView(selectedMarker.getLatLng(), 15);

                                // Mostrar popup detallado
                                const client = clients.find(c => c.id == value ||
                                    `${c.latitude}-${c.longitude}` == value);
                                const clickPopupContent = `
                                    <div class="client-popup">
                                        <h3>${client.nombres} ${client.apellidos}</h3>
                                        <p>${client.direccion}</p>
                                        <p style="color: #666; font-size: 12px;">${client.coordenadas}</p>
                                        <div style="margin-top: 8px; text-align: center;">
                                            <p style="color: #4a5568; margin-bottom: 2px;">
                                                <strong>Empleado Asignado</strong>
                                            </p>
                                            <p style="color: #4a5568;">${client.empleado_asignado}</p>
                                        </div>
                                    </div>
                                `;
                                selectedMarker.bindPopup(clickPopupContent).openPopup();

                                // Resaltar el marcador
                                selectedMarker.getElement()?.classList.add('highlighted-marker');
                            }
                        }
                    });

                    // Inicializar el filtro de empleados
                    const employeeFilter = new TomSelect('#employeeFilter', {
                        placeholder: 'Filtrar por empleado...',
                        onChange: function(value) {
                            // Ocultar todos los marcadores
                            markers.clearLayers();

                            // Filtrar clientes por empleado
                            clients.forEach(client => {
                                if (!value || client.employee_id == value) {
                                    const marker = markersById[client.id];
                                    if (marker) {
                                        markers.addLayer(marker);
                                    }
                                }
                            });

                            // Ajustar el zoom si hay marcadores visibles
                            if (markers.getLayers().length > 0) {
                                map.fitBounds(markers.getBounds(), {
                                    padding: [50, 50]
                                });
                            }
                        }
                    });
                });
            </script>
        @endpush
    </div>
</x-filament::page>
