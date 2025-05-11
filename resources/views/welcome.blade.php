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
            background: rgba(255,255,255,0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
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
    <script>
        var map = L.map('map').setView([36.1412, -5.4384], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markers = [];

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