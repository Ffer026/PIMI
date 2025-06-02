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

        /* Estilos para el menú de capas */
        .leaflet-control-layers {
            border-radius: 5px;
            background: white;
            padding: 10px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.4);
        }

        .leaflet-control-layers-toggle {
            background-image: none;
            width: auto;
            height: auto;
            padding: 5px 10px;
        }

        .leaflet-control-layers-expanded {
            padding: 10px;
        }

        /* Estilos para los botones de creación */
        .create-btn {
            margin-top: 10px;
            width: 100%;
        }

        /* Estilos para el menú de control */
        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.4);
        }

        .dropdown-menu {
            min-width: 200px;
        }

        /* Estilos para los formularios */
        .creation-form {
            margin-top: 10px;
            display: none;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control-sm {
            font-size: 0.85rem;
        }

        .form-actions {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        .required-field::after {
            content: " *";
            color: red;
        }
        
        .temp-marker {
            background-color: #dc3545;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .zone-link {
            cursor: pointer;
            color: #0d6efd;
            text-decoration: underline;
        }
        
        .zone-link:hover {
            color: #0a58ca;
        }
    </style>