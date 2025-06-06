<script>
    var map = L.map('map').setView([36.134079, -5.438576], 14); // Centro en Puerto de Algeciras

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // polygons contiene todos los polígonos del mapa
    var polygons = [];
    // Las llaves identifican un tipo de objeto similar a un Hashmap
    var portLayers = {};
    var zoneLayers = {};

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
        const date = new Date(dateString); // De string a objeto Date
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }); // Formato: 31 de diciembre de 2025, 12:00
    }

    // Función para crear el HTML del popup de puerto
    function createPortPopupContent(puerto) { //Puerto es un objeto con los datos del puerto recién sacados de la API
        //creation-form está nodisplay por defecto, se muestra al clickar en el botón de crear zona
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
                            <li><strong>Estado:</strong> ${puerto.activo ? 'Activo' : 'Inactivo'}</li>
                        </ul>
                    </div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Zonas en este puerto</div>
                        <div id="port-zones-list-${puerto.id_puerto}">
                            <p>Cargando zonas...</p>
                        </div>
                    </div>
                    
                    <button class="btn btn-sm btn-success create-btn" onclick="showCreateZoneForm(${puerto.id_puerto}, event)">
                        <i class="bi bi-plus-circle"></i> Crear Nueva Zona
                    </button>
                    
                    <div id="create-zone-form-${puerto.id_puerto}" class="creation-form">
                        <form onsubmit="createZone(event, ${puerto.id_puerto})">
                            <div class="form-group">
                                <label class="form-label required-field">Nombre</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Nombre de la zona" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Código de la zona (opcional)">
                            </div>
                            <div class="form-group">
                                <label class="form-label required-field">Coordenadas</label>
                                <textarea class="form-control form-control-sm" rows="3" placeholder="Formato: lat1,lng1;lat2,lng2;..." required></textarea>
                                <small class="text-muted">Introduce las coordenadas de los vértices del polígono</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label required-field">Espacios para contenedores</label>
                                <input type="number" class="form-control form-control-sm" min="1" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Niveles de apilamiento</label>
                                <input type="number" class="form-control form-control-sm" min="1" value="4">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control form-control-sm" rows="2" placeholder="Observaciones (opcional)"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="hideCreateZoneForm(${puerto.id_puerto})">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
    }

    // Función para crear el HTML del popup de zona
    function createZonePopupContent(zona) {// Zona es un objeto con los datos del puerto recién sacados de la API
        // creation-form está nodisplay por defecto, se muestra al clickar en el botón de crear zona
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
                            <li><strong>Estado:</strong> ${zona.activa ? 'Activa' : 'Inactiva'}</li>
                        </ul>
                        <p><strong>Observaciones:</strong> ${zona.observaciones || 'Ninguna'}</p>
                    </div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Espacios en esta zona</div>
                        <div id="zone-spaces-list-${zona.id_zona}">
                            <p>Cargando espacios...</p>
                        </div>
                    </div>
                    
                    <button class="btn btn-sm btn-success create-btn" onclick="showCreateSpaceForm(${zona.id_zona}, event)">
                        <i class="bi bi-plus-circle"></i> Crear Nuevo Espacio
                    </button>
                    
                    <div id="create-space-form-${zona.id_zona}" class="creation-form">
                        <form onsubmit="createSpace(event, ${zona.id_zona})">
                            <div class="form-group">
                                <label class="form-label required-field">Nombre</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Nombre del espacio" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Código del espacio (opcional)">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control form-control-sm" rows="2" placeholder="Observaciones (opcional)"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="hideCreateSpaceForm(${zona.id_zona})">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
    }

    // Función para mostrar el formulario de creación de zona
    function showCreateZoneForm(portId, event) {
        event.stopPropagation(); // No activa otros listeners
        document.getElementById(`create-zone-form-${portId}`).style.display = 'block';
    }

    // Función para ocultar el formulario de creación de zona
    function hideCreateZoneForm(portId) {
        document.getElementById(`create-zone-form-${portId}`).style.display = 'none';
    }

    // Función para crear una nueva zona
    function createZone(event, portId) {
        event.preventDefault(); // No recarga
        event.stopPropagation(); // No otros listeners

        const form = event.target;
        const zoneData = { // "Hashmap" tipoDato:Info
            id_puerto: portId,
            nombre: form.querySelector('input[type="text"]').value,
            codigo_zona: form.querySelectorAll('input[type="text"]')[1].value,
            coordenadas_vertices: form.querySelector('textarea').value,
            espacios_para_contenedores: form.querySelector('input[type="number"]').value,
            max_niveles_apilamiento: form.querySelectorAll('input[type="number"]')[1].value,
            observaciones: form.querySelectorAll('textarea')[1].value,
            activa: true
        };

        // Validar coordenadas
        try {
            const coords = convertirCoordenadas(zoneData.coordenadas_vertices);
            if (coords.length < 3) {
                throw new Error('Se necesitan al menos 3 puntos para crear un polígono');
            }
        } catch (e) {
            alert('Error en el formato de coordenadas: ' + e.message);
            return;
        }

        // Enviar datos al servidor
        axios.post('/api/zonas', zoneData)
            .then(response => {
                // Crear el polígono de la zona en el mapa
                const coords = convertirCoordenadas(response.data.coordenadas_vertices);
                const polygon = L.polygon(coords, {
                    color: '#28a745',
                    fillColor: '#28a745',
                    fillOpacity: 0.3,
                    weight: 2
                }).addTo(map);

                // Agregar tooltip
                polygon.bindTooltip(response.data.nombre, {
                    permanent: false,
                    direction: 'top',
                    className: 'map-tooltip'
                });

                // Agregar popup con información detallada
                polygon.bindPopup(createZonePopupContent(response.data));

                // Al abrir el popup, cargar los espacios de la zona
                polygon.on('popupopen', function(e) {
                    loadSpacesForZone(response.data.id_zona);
                });

                polygons.push(polygon);
                zoneLayers[response.data.id_zona] = polygon;

                // Actualizar la lista de zonas en el popup del puerto
                loadZonesForPort(portId);

                // Ocultar el formulario
                hideCreateZoneForm(portId);

                // Mostrar mensaje de éxito
                alert('Zona creada con éxito');
            })
            .catch(error => {
                console.error('Error al crear zona:', error);
                if (error.response) {
                    alert('Error al crear zona: ' + (error.response.data.message || error.response.statusText));
                } else {
                    alert('Error al crear zona: ' + error.message);
                }
            });
    }

    // Función para mostrar el formulario de creación de espacio
    function showCreateSpaceForm(zoneId, event) {
        event.stopPropagation(); // Que no active otros listeners
        document.getElementById(`create-space-form-${zoneId}`).style.display = 'block';
    }

    // Función para ocultar el formulario de creación de espacio
    function hideCreateSpaceForm(zoneId) {
        document.getElementById(`create-space-form-${zoneId}`).style.display = 'none';
    }

    // Función para crear un nuevo espacio
    function createSpace(event, zoneId) {
        event.preventDefault(); // Que no recargue la página al hacer post
        event.stopPropagation(); // Que no active otros listeners

        const form = event.target;
        const spaceData = { // Crea un "hasmap" para subir los datos al servidor asociados a cada tipo de dato
            id_zona: zoneId,
            nombre: form.querySelector('input[type="text"]').value, // El primer campo de texto, posición [0] si fuera All
            codigo_espacio: form.querySelectorAll('input[type="text"]')[1].value, // Segundo campo de texto
            observaciones: form.querySelector('textarea').value,
            activa: true
        };

        // Enviar datos al servidor
        axios.post('/api/espacios', spaceData)
            .then(response => {
                // Actualizar la lista de espacios en el popup de la zona
                loadSpacesForZone(zoneId);

                // Ocultar el formulario
                hideCreateSpaceForm(zoneId);

                // Mostrar mensaje de éxito
                alert('Espacio creado con éxito');
            })
            .catch(error => {
                console.error('Error al crear espacio:', error); // Aviso en consola
                if (error.response) { // Alertas con la información
                    alert('Error al crear espacio: ' + (error.response.data.message || error.response.statusText));
                } else {
                    alert('Error al crear espacio: ' + error.message);
                }
            });
    }

    // Función para cargar los puertos desde la API
    function loadPuertos() {
        axios.get('/api/puertos')
            .then(response => {
                response.data.forEach(puerto => {
                    try {
                        const coords = convertirCoordenadas(puerto.coordenadas_vertices); // De string a array de coordenadas 
                        if (coords.length === 0) return;

                        const polygon = L.polygon(coords, { // Características del polígono o puerto
                            color: '#0056b3',
                            fillColor: '#007bff',
                            fillOpacity: 0.4,
                            weight: 3
                        }).addTo(map);

                        polygon.bindTooltip(puerto.nombre, { // La info que aparece al pasar el ratón por encima
                            permanent: false,
                            direction: 'top',
                            className: 'map-tooltip'
                        });

                        polygon.bindPopup(createPortPopupContent(puerto)); // El popup al clickar atado al polígono

                        polygon.on('popupopen', function(e) {
                            loadZonesForPort(puerto.id_puerto); // Cargamos las zonas de ese puerto en el popup del polígono
                        });

                        polygons.push(polygon); // Metemos el polígono en el array de polígonos
                        portLayers[puerto.id_puerto] = polygon; // Y en el "hashmap" de puertos asociado a su id
                    } catch (e) {
                        console.error('Error al procesar puerto:', puerto.id_puerto, e);
                    }
                });
            })
            .catch(error => {
                console.error('Error al cargar puertos:', error);
            });
    }

    // Función para cargar las zonas de un puerto específico dentro de su popup
    function loadZonesForPort(portId) {
        axios.get(`/api/puertos/${portId}/zonas`)
            .then(response => {
                /* El objeto response.data es un array de objetos zonas,
                map() los convierte en elementos HTML
                join() convierte el array en un string largo*/
                const zonesList = response.data.map(zona =>
                    `<li><span class="zone-link" onclick="highlightZone(${zona.id_zona}, event)">${zona.nombre}</span> (${zona.codigo_zona})</li>`
                ).join('');

                const content = zonesList.length > 0 ?
                    `<ul class="popup-list">${zonesList}</ul>` : // Metemos las zonas li en ul
                    '<p>No hay zonas registradas para este puerto</p>'; // Si hay 0 zonas

                document.getElementById(`port-zones-list-${portId}`).innerHTML = content; // content contiene la lista
            })
            .catch(error => {
                console.error('Error al cargar zonas del puerto:', error);
                document.getElementById(`port-zones-list-${portId}`).innerHTML =
                    '<p>Error al cargar las zonas</p>';
            });
    }

    // Función para cargar todas las zonas en el mapa
    function loadZonas() {
        axios.get('/api/zonas')
            .then(response => {
                response.data.forEach(zona => {
                    try {
                        const coords = convertirCoordenadas(zona.coordenadas_vertices); // coords es un array de coordenadas
                        if (coords.length === 0) return; // Si coords está vacío, acaba la función

                        const polygon = L.polygon(coords, { // El color de las zonas
                            color: '#28a745',
                            fillColor: '#28a745',
                            fillOpacity: 0.3,
                            weight: 2
                        }).addTo(map); // Se añade al mapa el polígono que es la zona

                        // Esto muestra el nombre de la zona solo al pasar el ratón por encima
                        polygon.bindTooltip(zona.nombre, {
                            permanent: false,
                            direction: 'top',
                            className: 'map-tooltip'
                        });

                        // Ata un popup al polígono, cuya info es el string que devuelve createZonePopupContent
                        polygon.bindPopup(createZonePopupContent(zona));

                        // Al abrir el popup, cargar los espacios de la zona en el HTML
                        polygon.on('popupopen', function(e) {
                            loadSpacesForZone(zona.id_zona);
                        });

                        polygons.push(polygon); //Metemos el polígono en el array de polígonos...
                        zoneLayers[zona.id_zona] = polygon; // ...y en zoneLayers asociado a la ID de la zona

                    } catch (e) { // Error con la creación de la zona
                        console.error('Error al procesar zona:', zona.id_zona, e);
                    }
                });
            })
            .catch(error => { // Error con la petición al servidor
                console.error('Error al cargar zonas:', error);
            });
    }

    // Función para cargar los espacios de una zona específica
    // Estos datos se muestran en el HTML de createZonePopupContent
    function loadSpacesForZone(zoneId) {
        axios.get(`/api/zonas/${zoneId}/espacios`)
            .then(response => {
                /* response.data es un array de objetos que son los espacios de contenedores con sus datos y todo
                    map() los convierte en elementos html
                    y join los concierte en un string, que ya se guarda en spacesList*/
                const spacesList = response.data.map(espacio =>
                    `<li><a href="/api/espacios/${espacio.id_espacios_contenedores}/contenedores">${espacio.nombre}</a> (${espacio.codigo_espacio})</li>`
                ).join('');

                const content = spacesList.length > 0 ? // Condición
                    // Si hay espacios haz un elemento ul que contenga sapacesList (que tiene elementos il)
                    `<ul class="popup-list">${spacesList}</ul>` :
                    '<p>No hay espacios registrados para esta zona</p>'; // Si no hay espacios lo muestra con un mensaje

                document.getElementById(`zone-spaces-list-${zoneId}`).innerHTML = content;
            })
            .catch(error => { // Da error en la consola y en la zona, para que se vea bien
                console.error('Error al cargar espacios de la zona:', error);
                document.getElementById(`zone-spaces-list-${zoneId}`).innerHTML =
                    '<p>Error al cargar los espacios</p>';
            });
    }

    // Función para resaltar una zona en el mapa
    function highlightZone(zoneId, event) {
        if (event) event.stopPropagation(); //Prevenimos la propagación por el DOM

        // Resetear todos los estilos primero, por si ya hay otra zona resaltada
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

    document.addEventListener('DOMContentLoaded', function() {
        //Cargamos los puertos y las zonas
        loadPuertos();
        loadZonas();

        // Botón de ver puertos
        /* Añade que si cambias el estado del botón añade o elimina cada puerto, aquí llamado capa o layer.
            Con Object.values(portLayers) el objeto se transforma en un array de valores, los cuales se pueden
            poner o quitar del mapa.*/
        document.getElementById('showPortsToggle').addEventListener('change', function(e) {
            Object.values(portLayers).forEach(layer => {
                if (e.target.checked) {
                    map.addLayer(layer);
                } else {
                    map.removeLayer(layer);
                }
            });
        });
        // Botón de ver zonas, igual que el botón de puertos pero para zonas
        document.getElementById('showZonesToggle').addEventListener('change', function(e) {
            Object.values(zoneLayers).forEach(layer => {
                if (e.target.checked) {
                    map.addLayer(layer);
                } else {
                    map.removeLayer(layer);
                }
            });
        });
    });

    // Manejar clics en el mapa fuera de polígonos
    map.on('click', function(e) {
        let isInsidePolygon = false;

        polygons.forEach(polygon => {
            if (polygon.getBounds().contains(e.latlng)) { //Si el click está en un polígono
                isInsidePolygon = true;
            }
        });

        // Si no está dentro de ningún polígono, mostrar coordenadas
        if (!isInsidePolygon) {
            var popupContent = `
                    <div class="popup-container">
                        <h3 class="popup-title">Coordenadas</h3>
                        <div class="popup-section">
                            <p><strong>Latitud:</strong> ${e.latlng.lat.toFixed(6)}</p>
                            <p><strong>Longitud:</strong> ${e.latlng.lng.toFixed(6)}</p>
                        </div>
                    </div>
                `;

            L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);
        }
    });
</script>
