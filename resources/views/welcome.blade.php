<!DOCTYPE html>
<html>

<head>
    <title>PIMI - Puerto Interactivo Mapeado Integrable</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            width: 100%;
            height: 100vh;
        }

        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .info h4 {
            margin: 0 0 5px;
            color: #777;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Incluir Axios para hacer peticiones HTTP -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        var map = L.map('map').setView([36.1412, -5.4384], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markers = [];
        var polygons = [];

        // Dimensiones estándar de contenedores (en metros)
        const CONTAINER_WIDTH = 2.44; // Ancho estándar de contenedor
        const CONTAINER_LENGTH = 6.06; // Largo estándar de contenedor (20 pies)

        // Función para convertir string de coordenadas a formato Leaflet
        function parseSingleCoordinate(coordString) {
            const [lat, lng] = coordString.trim().split(' ');
            return [parseFloat(lat), parseFloat(lng)];
        }
        
        // Función para calcular las coordenadas del polígono de la zona
        function calculateZonePolygon(topLeftCoord, filas, contenedoresPorFila, separacionEntreFilas, inclinacionGrados) {
            // Convertir la inclinación a radianes
            const inclinacionRad = (inclinacionGrados * Math.PI) / 180;

            // Calcular dimensiones totales
            const totalLength = contenedoresPorFila * CONTAINER_LENGTH;
            const totalWidth = filas * CONTAINER_WIDTH + (filas - 1) * separacionEntreFilas;

            // Coordenadas iniciales (esquina superior izquierda)
            const [lat0, lng0] = topLeftCoord;

            // Calcular las otras esquinas
            // Esquina superior derecha
            const lat1 = lat0 + totalLength * Math.sin(inclinacionRad) / 111320;
            const lng1 = lng0 + totalLength * Math.cos(inclinacionRad) / (111320 * Math.cos(lat0 * Math.PI / 180));

            // Esquina inferior derecha
            const lat2 = lat1 - totalWidth * Math.cos(inclinacionRad) / 111320;
            const lng2 = lng1 + totalWidth * Math.sin(inclinacionRad) / (111320 * Math.cos(lat0 * Math.PI / 180));

            // Esquina inferior izquierda
            const lat3 = lat0 - totalWidth * Math.cos(inclinacionRad) / 111320;
            const lng3 = lng0 + totalWidth * Math.sin(inclinacionRad) / (111320 * Math.cos(lat0 * Math.PI / 180));

            return [
                [lat0, lng0], // Superior izquierda
                [lat1, lng1], // Superior derecha
                [lat2, lng2], // Inferior derecha
                [lat3, lng3], // Inferior izquierda
                [lat0, lng0] // Cerrar el polígono
            ];
        }

        // Función para cargar los puertos desde la API
        function loadPuertos() {
            axios.get('/api/puertos')
                .then(response => {
                    response.data.forEach(puerto => {
                        try {
                            const coords = convertirCoordenadas(puerto.coordenadas_vertices);
                            const polygon = L.polygon(coords, {
                                color: 'red',
                                fillColor: '#f03',
                                fillOpacity: 0.5,
                                weight: 2
                            }).addTo(map);

                            polygon.bindPopup(`
                            <b>${puerto.nombre}</b><br>
                            <small>Código: ${puerto.codigo}</small><br>
                            ${puerto.descripcion || ''}
                        `);

                            polygons.push(polygon);
                        } catch (e) {
                            console.error('Error al procesar puerto:', puerto.id_puerto, e);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error al cargar puertos:', error);
                });
        }

        // Función para cargar las zonas desde la API
        function loadZonas() {
            axios.get('/api/zonas')
                .then(response => {
                    response.data.forEach(zona => {
                        try {
                            const coords = convertirCoordenadas(zona.coordenadas_vertices);
                            const polygon = L.polygon(coords, {
                                color: 'blue',
                                fillColor: '#03f',
                                fillOpacity: 0.3,
                                weight: 2
                            }).addTo(map);
                            
                            polygon.bindPopup(`
                                <b>${zona.nombre}</b><br>
                                <small>Código: ${zona.codigo_zona || 'N/A'}</small><br>
                                Filas: ${zona.filas}<br>
                                Contenedores por fila: ${zona.contenedores_por_fila}<br>
                                Inclinación: ${zona.inclinacion_grados || 0}°
                                `
                                );
                            
                            polygons.push(polygon);
                        } catch (e) {
                            console.error('Error al procesar zona:', zona.id_zona, e);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error al cargar zonas:', error);
                });
        }

        function convertirCoordenadas(strCoordenadas) {
            // Dividir el string por los punto y coma para obtener los pares
            const pares = strCoordenadas.split(';');

            // Mapear cada par a un array de números
            return pares.map(par => {
                // Dividir cada par por la coma
                const coordenadas = par.split(',');
                // Convertir cada parte a número flotante
                return [
                    parseFloat(coordenadas[0]),
                    parseFloat(coordenadas[1])
                ];
            });
        }

        // Cargar los datos al iniciar el mapa
        document.addEventListener('DOMContentLoaded', function() {
            loadPuertos();
            loadZonas();
        });

        map.on('click', function(e) {
            // Crear el contenido del popup
            var popupContent = `
                <div>
                    <h3>Coordenadas</h3>
                    <p>Latitud: ${e.latlng.lat.toFixed(4)}</p>
                    <p>Longitud: ${e.latlng.lng.toFixed(4)}</p>
                    <hr>
                    <h3>Crear marcador</h3>
                    <form id="markerForm">
                        <label for="markerName">Nombre:</label>
                        <input type="text" id="markerName" required><br><br>
                        <label for="markerDesc">Descripción:</label>
                        <textarea id="markerDesc"></textarea><br><br>
                        <button type="submit">Crear marcador</button>
                    </form>
                </div>
            `;

            // Crear popup
            var popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);

            // Esperar a que el popup se renderice antes de agregar el event listener
            setTimeout(() => {
                document.getElementById('markerForm').addEventListener('submit', function(ev) {
                    ev.preventDefault();

                    // Obtener valores
                    var name = document.getElementById('markerName').value;
                    var desc = document.getElementById('markerDesc').value;

                    // Crear marcador
                    var newMarker = L.marker(e.latlng)
                        .addTo(map)
                        .bindPopup(`<b>${name}</b><br>${desc}`);

                    // Añadir al array
                    markers.push(newMarker);

                    // Cerrar popup
                    map.closePopup();
                });
            }, 100);
        });
    </script>
</body>

</html>
