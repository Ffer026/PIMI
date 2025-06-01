<!DOCTYPE html>
<html>

<head>
    <title>PIMI - Puerto Interactivo Mapeado Integrable</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <!-- Bootstrap CSS para mejorar el estilo de los popups -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #map {
            width: 100%;
            height: 100vh;
        }

        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .info h4 {
            margin: 0 0 5px;
            color: #333;
            font-weight: bold;
        }

        .popup-container {
            max-width: 300px;
        }

        .popup-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }

        .popup-subtitle {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }

        .popup-section {
            margin-bottom: 0.75rem;
        }

        .popup-section-title {
            font-weight: bold;
            margin-bottom: 0.25rem;
            color: #495057;
        }

        .popup-list {
            padding-left: 1.25rem;
            margin-bottom: 0;
        }

        .badge-container {
            margin-bottom: 0.5rem;
        }

        .material-peligroso {
            background-color: #dc3545;
            color: white;
        }

        .material-seguro {
            background-color: #28a745;
            color: white;
        }

        .leaflet-popup-content {
            margin: 12px 15px;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Incluir Axios para hacer peticiones HTTP -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Bootstrap JS para los tooltips -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        var map = L.map('map').setView([40.4168, -3.7038], 6); // Centro en España

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markers = [];
        var polygons = [];
        var portLayers = {};
        var zoneLayers = {};
        var containerLayers = {};

        // Función para convertir string de coordenadas a formato Leaflet
        function parseSingleCoordinate(coordString) {
            const [lat, lng] = coordString.trim().split(' ');
            return [parseFloat(lat), parseFloat(lng)];
        }

        // Función para convertir coordenadas del string a array
        function convertirCoordenadas(strCoordenadas) {
            if (!strCoordenadas) return [];
            const pares = strCoordenadas.split(';');
            return pares.map(par => {
                const coordenadas = par.split(',');
                return [
                    parseFloat(coordenadas[0]),
                    parseFloat(coordenadas[1])
                ];
            });
        }

        // Función para formatear fechas
        function formatDate(dateString) {
            if (!dateString) return 'No disponible';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Función para crear el contenido del popup de un puerto
        function createPortPopupContent(puerto) {
            return `
                <div class="popup-container">
                    <div class="popup-title">${puerto.nombre}</div>
                    <div class="popup-subtitle">Código: ${puerto.codigo}</div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Información del Puerto</div>
                        <p>${puerto.descripcion || 'No hay descripción disponible'}</p>
                        <ul class="popup-list">
                            <li><strong>Dirección:</strong> ${puerto.direccion || 'No disponible'}</li>
                            <li><strong>Teléfono:</strong> ${puerto.telefono_contacto || 'No disponible'}</li>
                            <li><strong>Fecha creación:</strong> ${formatDate(puerto.fecha_creacion)}</li>
                            <li><strong>Última actualización:</strong> ${formatDate(puerto.ultima_actualizacion)}</li>
                        </ul>
                    </div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Zonas en este puerto</div>
                        <div id="port-zones-list-${puerto.id_puerto}">
                            <p>Cargando zonas...</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Función para crear el contenido del popup de una zona
        function createZonePopupContent(zona) {
            return `
                <div class="popup-container">
                    <div class="popup-title">${zona.nombre}</div>
                    <div class="popup-subtitle">Código: ${zona.codigo_zona || 'N/A'}</div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Detalles de la Zona</div>
                        <ul class="popup-list">
                            <li><strong>Capacidad:</strong> ${zona.espacios_para_contenedores || '0'} contenedores</li>
                            <li><strong>Niveles apilamiento:</strong> ${zona.max_niveles_apilamiento || 'No especificado'}</li>
                            <li><strong>Fecha creación:</strong> ${formatDate(zona.fecha_creacion)}</li>
                            <li><strong>Última actualización:</strong> ${formatDate(zona.ultima_actualizacion)}</li>
                        </ul>
                        <p><strong>Observaciones:</strong> ${zona.observaciones || 'Ninguna'}</p>
                    </div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Espacios en esta zona</div>
                        <div id="zone-spaces-list-${zona.id_zona}">
                            <p>Cargando espacios...</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Función para crear el contenido del popup de un espacio de contenedores
        function createSpacePopupContent(espacio) {
            return `
                <div class="popup-container">
                    <div class="popup-title">${espacio.nombre}</div>
                    <div class="popup-subtitle">Código: ${espacio.codigo_espacio}</div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Detalles del Espacio</div>
                        <p><strong>Observaciones:</strong> ${espacio.observaciones || 'Ninguna'}</p>
                        <p><strong>Última actualización:</strong> ${formatDate(espacio.ultima_actualizacion)}</p>
                    </div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Contenedores en este espacio</div>
                        <div id="space-containers-list-${espacio.id_espacios_contenedores}">
                            <p>Cargando contenedores...</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Función para crear el contenido del popup de un contenedor
        function createContainerPopupContent(contenedor, tipoContenedor) {
            const peligrosoClass = contenedor.material_peligroso ? 'material-peligroso' : 'material-seguro';
            const peligrosoText = contenedor.material_peligroso ? 'Material Peligroso' : 'Material Seguro';
            
            return `
                <div class="popup-container">
                    <div class="popup-title">Contenedor ${tipoContenedor.iso_code}</div>
                    <div class="popup-subtitle">Tipo: ${tipoContenedor.descripcion}</div>
                    
                    <div class="badge-container">
                        <span class="badge ${peligrosoClass}">${peligrosoText}</span>
                    </div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Especificaciones</div>
                        <ul class="popup-list">
                            <li><strong>Dimensiones:</strong> ${tipoContenedor.longitud_m}m x ${tipoContenedor.anchura_m}m x ${tipoContenedor.altura_m}m</li>
                            <li><strong>Peso máximo:</strong> ${tipoContenedor.peso_max_kg} kg</li>
                            <li><strong>Propietario:</strong> ${contenedor.propietario || 'Desconocido'}</li>
                            <li><strong>Ubicado desde:</strong> ${formatDate(contenedor.created_at)}</li>
                            <li><strong>Última actualización:</strong> ${formatDate(contenedor.updated_at)}</li>
                        </ul>
                    </div>
                </div>
            `;
        }

        // Función para cargar los puertos desde la API
        function loadPuertos() {
            axios.get('/api/puertos')
                .then(response => {
                    response.data.forEach(puerto => {
                        try {
                            const coords = convertirCoordenadas(puerto.coordenadas_vertices);
                            if (coords.length === 0) return;
                            
                            const polygon = L.polygon(coords, {
                                color: '#0056b3',
                                fillColor: '#007bff',
                                fillOpacity: 0.4,
                                weight: 3
                            }).addTo(map);
                            
                            // Agregar tooltip
                            polygon.bindTooltip(puerto.nombre, {
                                permanent: false,
                                direction: 'top',
                                className: 'map-tooltip'
                            });
                            
                            // Agregar popup con información detallada
                            polygon.bindPopup(createPortPopupContent(puerto));
                            
                            // Al abrir el popup, cargar las zonas del puerto
                            polygon.on('popupopen', function(e) {
                                loadZonesForPort(puerto.id_puerto);
                            });
                            
                            polygons.push(polygon);
                            portLayers[puerto.id_puerto] = polygon;
                        } catch (e) {
                            console.error('Error al procesar puerto:', puerto.id_puerto, e);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error al cargar puertos:', error);
                });
        }

        // Función para cargar las zonas de un puerto específico
        function loadZonesForPort(portId) {
            axios.get(`/api/puertos/${portId}/zonas`)
                .then(response => {
                    const zonesList = response.data.map(zona => 
                        `<li><a href="#" onclick="highlightZone(${zona.id_zona}); return false;">${zona.nombre}</a> (${zona.codigo_zona})</li>`
                    ).join('');
                    
                    const content = zonesList.length > 0 ? 
                        `<ul class="popup-list">${zonesList}</ul>` : 
                        '<p>No hay zonas registradas para este puerto</p>';
                    
                    document.getElementById(`port-zones-list-${portId}`).innerHTML = content;
                })
                .catch(error => {
                    console.error('Error al cargar zonas del puerto:', error);
                    document.getElementById(`port-zones-list-${portId}`).innerHTML = 
                        '<p>Error al cargar las zonas</p>';
                });
        }

        // Función para cargar todas las zonas
        function loadZonas() {
            axios.get('/api/zonas')
                .then(response => {
                    response.data.forEach(zona => {
                        try {
                            const coords = convertirCoordenadas(zona.coordenadas_vertices);
                            if (coords.length === 0) return;
                            
                            const polygon = L.polygon(coords, {
                                color: '#28a745',
                                fillColor: '#28a745',
                                fillOpacity: 0.3,
                                weight: 2
                            }).addTo(map);
                            
                            // Agregar tooltip
                            polygon.bindTooltip(zona.nombre, {
                                permanent: false,
                                direction: 'top',
                                className: 'map-tooltip'
                            });
                            
                            // Agregar popup con información detallada
                            polygon.bindPopup(createZonePopupContent(zona));
                            
                            // Al abrir el popup, cargar los espacios de la zona
                            polygon.on('popupopen', function(e) {
                                loadSpacesForZone(zona.id_zona);
                            });
                            
                            polygons.push(polygon);
                            zoneLayers[zona.id_zona] = polygon;
                        } catch (e) {
                            console.error('Error al procesar zona:', zona.id_zona, e);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error al cargar zonas:', error);
                });
        }

        // Función para cargar los espacios de una zona específica
        function loadSpacesForZone(zoneId) {
            axios.get(`/api/zonas/${zoneId}/espacios`)
                .then(response => {
                    const spacesList = response.data.map(espacio => 
                        `<li><a href="#" onclick="highlightSpace(${espacio.id_espacios_contenedores}); return false;">${espacio.nombre}</a> (${espacio.codigo_espacio})</li>`
                    ).join('');
                    
                    const content = spacesList.length > 0 ? 
                        `<ul class="popup-list">${spacesList}</ul>` : 
                        '<p>No hay espacios registrados para esta zona</p>';
                    
                    document.getElementById(`zone-spaces-list-${zoneId}`).innerHTML = content;
                })
                .catch(error => {
                    console.error('Error al cargar espacios de la zona:', error);
                    document.getElementById(`zone-spaces-list-${zoneId}`).innerHTML = 
                        '<p>Error al cargar los espacios</p>';
                });
        }

        // Función para cargar los contenedores de un espacio específico
        function loadContainersForSpace(spaceId) {
            axios.get(`/api/espacios/${spaceId}/contenedores`)
                .then(response => {
                    // Primero necesitamos obtener los tipos de contenedor para mostrar información completa
                    axios.get('/api/tipos-contenedor')
                        .then(tiposResponse => {
                            const tiposMap = {};
                            tiposResponse.data.forEach(tipo => {
                                tiposMap[tipo.iso_code] = tipo;
                            });
                            
                            const containersList = response.data.map(contenedor => {
                                const tipo = tiposMap[contenedor.tipo_contenedor_iso] || {};
                                return `
                                    <li>
                                        <a href="#" onclick="highlightContainer(${contenedor.id_contenedor}); return false;">
                                            Contenedor ${tipo.iso_code || contenedor.tipo_contenedor_iso}
                                        </a>
                                        <span class="badge ${contenedor.material_peligroso ? 'material-peligroso' : 'material-seguro'}">
                                            ${contenedor.material_peligroso ? 'Peligroso' : 'Seguro'}
                                        </span>
                                    </li>
                                `;
                            }).join('');
                            
                            const content = containersList.length > 0 ? 
                                `<ul class="popup-list">${containersList}</ul>` : 
                                '<p>No hay contenedores registrados en este espacio</p>';
                            
                            document.getElementById(`space-containers-list-${spaceId}`).innerHTML = content;
                        })
                        .catch(error => {
                            console.error('Error al cargar tipos de contenedor:', error);
                            document.getElementById(`space-containers-list-${spaceId}`).innerHTML = 
                                '<p>Error al cargar los contenedores</p>';
                        });
                })
                .catch(error => {
                    console.error('Error al cargar contenedores del espacio:', error);
                    document.getElementById(`space-containers-list-${spaceId}`).innerHTML = 
                        '<p>Error al cargar los contenedores</p>';
                });
        }

        // Función para resaltar una zona en el mapa
        function highlightZone(zoneId) {
            // Resetear todos los estilos primero
            Object.values(zoneLayers).forEach(layer => {
                layer.setStyle({
                    color: '#28a745',
                    fillColor: '#28a745',
                    fillOpacity: 0.3,
                    weight: 2
                });
            });
            
            // Resaltar la zona seleccionada
            if (zoneLayers[zoneId]) {
                zoneLayers[zoneId].setStyle({
                    color: '#ffc107',
                    fillColor: '#ffc107',
                    fillOpacity: 0.6,
                    weight: 3
                });
                map.fitBounds(zoneLayers[zoneId].getBounds());
                zoneLayers[zoneId].openPopup();
            }
        }

        // Función para resaltar un espacio en el mapa (como marcador)
        function highlightSpace(spaceId) {
            // En una implementación real, aquí buscarías las coordenadas del espacio
            // y lo resaltarías en el mapa. Por ahora mostramos un mensaje.
            alert(`Espacio ${spaceId} seleccionado. En una implementación completa, esto centraría el mapa en ese espacio.`);
        }

        // Función para resaltar un contenedor en el mapa
        function highlightContainer(containerId) {
            // En una implementación real, aquí buscarías las coordenadas del contenedor
            // y lo resaltarías en el mapa. Por ahora mostramos un mensaje.
            alert(`Contenedor ${containerId} seleccionado. En una implementación completa, esto centraría el mapa en ese contenedor.`);
        }

        // Cargar los datos al iniciar el mapa
        document.addEventListener('DOMContentLoaded', function() {
            loadPuertos();
            loadZonas();
            
            // Opcional: cargar tipos de contenedor al inicio para tenerlos disponibles
            axios.get('/api/tipos-contenedor')
                .then(response => {
                    console.log('Tipos de contenedor cargados:', response.data.length);
                })
                .catch(error => {
                    console.error('Error al cargar tipos de contenedor:', error);
                });
        });

        // Manejar clics en el mapa para crear nuevos marcadores
        map.on('click', function(e) {
            var popupContent = `
                <div class="popup-container">
                    <h3 class="popup-title">Coordenadas</h3>
                    <div class="popup-section">
                        <p><strong>Latitud:</strong> ${e.latlng.lat.toFixed(6)}</p>
                        <p><strong>Longitud:</strong> ${e.latlng.lng.toFixed(6)}</p>
                    </div>
                    
                    <hr>
                    
                    <h3 class="popup-title">Crear nuevo elemento</h3>
                    <div class="popup-section">
                        <form id="markerForm">
                            <div class="mb-3">
                                <label for="elementType" class="form-label">Tipo de elemento:</label>
                                <select class="form-select" id="elementType" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="marker">Marcador simple</option>
                                    <option value="container">Contenedor</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="elementName" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="elementName" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="elementDesc" class="form-label">Descripción:</label>
                                <textarea class="form-control" id="elementDesc" rows="2"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Crear</button>
                        </form>
                    </div>
                </div>
            `;

            var popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);

            setTimeout(() => {
                document.getElementById('markerForm').addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    
                    const elementType = document.getElementById('elementType').value;
                    const name = document.getElementById('elementName').value;
                    const desc = document.getElementById('elementDesc').value;
                    
                    if (elementType === 'marker') {
                        const newMarker = L.marker(e.latlng)
                            .addTo(map)
                            .bindPopup(`<b>${name}</b><br>${desc}`);
                        markers.push(newMarker);
                    } else if (elementType === 'container') {
                        // En una implementación real, aquí crearías un contenedor en la base de datos
                        const newMarker = L.marker(e.latlng, {
                            icon: L.divIcon({
                                className: 'container-marker',
                                html: '<div class="container-icon"><i class="bi bi-box-seam"></i></div>',
                                iconSize: [30, 30]
                            })
                        })
                        .addTo(map)
                        .bindPopup(`<b>Contenedor ${name}</b><br>${desc}`);
                        markers.push(newMarker);
                    }
                    
                    map.closePopup();
                });
            }, 100);
        });
    </script>
</body>

</html>