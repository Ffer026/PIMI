<!DOCTYPE html>
<html>
<head>
    <title>Mapa de Puerto Interactivo con OpenStreetMap</title>
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
        // Inicializar el mapa
        var map = L.map('map').setView([36.140969, -5.43849], 16);
        
        // Usar OpenStreetMap como capa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Añadir algunos marcadores de ejemplo para instalaciones del puerto
        var wharfMarker = L.marker([36.7196, -4.4190]).addTo(map)
            .bindPopup("<b>Muelle Principal</b><br>Longitud: 250m");
            
        var craneMarker = L.marker([36.7205, -4.4205]).addTo(map)
            .bindPopup("<b>Grúa de carga</b><br>Capacidad: 50 ton");
            
        var warehouseMarker = L.marker([36.7188, -4.4215]).addTo(map)
            .bindPopup("<b>Almacén portuario</b><br>Área: 5,000 m²");
        
        // Añadir un área de atraque
        var dockArea = L.polygon([
            [36.7200, -4.4180],
            [36.7205, -4.4185],
            [36.7195, -4.4195],
            [36.7190, -4.4190]
        ], {color: 'blue', fillOpacity: 0.2}).addTo(map)
        .bindPopup("<b>Zona de atraque</b><br>Profundidad: 12m");
        
        // Control de capas
        var baseLayers = {
            "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            })
        };
        
        var overlays = {
            "Muelle Principal": wharfMarker,
            "Grúa de carga": craneMarker,
            "Almacén": warehouseMarker,
            "Zona de atraque": dockArea
        };
        
        L.control.layers(baseLayers, overlays).addTo(map);
        
        // Añadir control de escala
        L.control.scale().addTo(map);
        
        // Mostrar coordenadas al hacer clic
        var popup = L.popup();
        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent("Coordenadas: " + e.latlng.toString())
                .openOn(map);
        }
        map.on('click', onMapClick);
    </script>
</body>
</html>