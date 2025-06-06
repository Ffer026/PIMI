<!DOCTYPE html>
<html>

<head>
    <title>PIMI - Puerto Interactivo Mapeado Integrable</title>
    <link rel="icon" type="image/png" href="{{ asset('Icono.png') }}?v={{ time() }}">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <x-style></x-style>
</head>

<body>
    <div id="map"></div>
    
    <!-- Menú de control en la esquina superior derecha -->
    <x-menu></x-menu>

    <x-imports></x-imports>
    
    <x-logic></x-logic>
</body>

</html>