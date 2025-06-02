<script>
    var map = L.map('map').setView([40.4168, -3.7038], 6); // Centro en España
    var clickMarker = null; // Marcador temporal para el punto de creación
    var currentPopup = null; // Referencia al popup actual

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var markers = [];
    // polygons contiene todos los polígonos del mapa
    var polygons = [];
    // Las llaves identifican un tipo de objeto similar a un Hashmap
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
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
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

    // Función para crear el contenido del popup de un espacio de contenedores
    function createSpacePopupContent(espacio) {
        return `
                <div class="popup-container">
                    <div class="popup-title">${espacio.nombre}</div>
                    <div class="popup-subtitle">Código: ${espacio.codigo_espacio}</div>
                    
                    <div class="popup-section">
                        <div class="popup-section-title">Detalles del Espacio</div>
                        <ul class="popup-list">
                            <li><strong>Estado:</strong> ${espacio.activa ? 'Activo' : 'Inactivo'}</li>
                            <li><strong>Última actualización:</strong> ${formatDate(espacio.ultima_actualizacion)}</li>
                        </ul>
                        <p><strong>Observaciones:</strong> ${espacio.observaciones || 'Ninguna'}</p>
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

    // Función para mostrar el formulario de creación de puerto
    function showCreatePortForm(latlng, event) {
        if (event) event.stopPropagation();

        // Crear marcador temporal
        if (clickMarker) map.removeLayer(clickMarker);
        clickMarker = L.marker(latlng, {
            icon: L.divIcon({
                className: 'temp-marker-icon',
                html: '<div class="temp-marker"></div>',
                iconSize: [24, 24]
            }),
            draggable: true
        }).addTo(map);

        clickMarker.on('dragend', function(e) {
            updatePortFormCoordinates(e.target.getLatLng());
        });

        // Mostrar formulario de creación de puerto
        const popupContent = `
                <div class="popup-container">
                    <div class="popup-title">Crear Nuevo Puerto</div>
                    
                    <div id="create-port-form">
                        <form onsubmit="createPort(event)">
                            <div class="form-group">
                                <label class="form-label required-field">Nombre</label>
                                <input type="text" class="form-control form-control-sm" id="port-name" placeholder="Nombre del puerto" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required-field">Código</label>
                                <input type="text" class="form-control form-control-sm" id="port-code" placeholder="Código único del puerto" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required-field">Coordenadas</label>
                                <textarea id="port-coordinates" class="form-control form-control-sm" rows="3" placeholder="Formato: lat1,lng1;lat2,lng2;..." required>${latlng.lat},${latlng.lng}</textarea>
                                <small class="text-muted">Introduce las coordenadas de los vértices del polígono</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control form-control-sm" id="port-description" rows="2" placeholder="Descripción (opcional)"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control form-control-sm" id="port-address" placeholder="Dirección (opcional)">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Teléfono de contacto</label>
                                <input type="text" class="form-control form-control-sm" id="port-phone" placeholder="Teléfono (opcional)">
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="port-active" checked>
                                <label class="form-check-label" for="port-active">Activo</label>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="cancelPortCreation()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

        currentPopup = L.popup()
            .setLatLng(latlng)
            .setContent(popupContent)
            .openOn(map);
    }

    // Función para actualizar coordenadas en el formulario de puerto
    function updatePortFormCoordinates(latlng) {
        const textarea = document.getElementById('port-coordinates');
        if (textarea) {
            textarea.value = `${latlng.lat},${latlng.lng}`;
        }
    }

    // Función para cancelar la creación de puerto
    function cancelPortCreation() {
        if (clickMarker) {
            map.removeLayer(clickMarker);
            clickMarker = null;
        }
        if (currentPopup) {
            map.closePopup(currentPopup);
            currentPopup = null;
        }
    }

    // Función para crear un nuevo puerto
    function createPort(event) {
        event.preventDefault();
        event.stopPropagation();

        // Obtener los datos del formulario
        const portData = {
            nombre: document.getElementById('port-name').value,
            codigo: document.getElementById('port-code').value,
            coordenadas_vertices: document.getElementById('port-coordinates').value,
            descripcion: document.getElementById('port-description').value,
            direccion: document.getElementById('port-address').value,
            telefono_contacto: document.getElementById('port-phone').value,
            activo: document.getElementById('port-active').checked
        };

        // Validar que las coordenadas tengan el formato correcto
        try {
            const coords = convertirCoordenadas(portData.coordenadas_vertices);
            if (coords.length === 0) {
                throw new Error('Las coordenadas no son válidas');
            }
        } catch (e) {
            alert('Error en el formato de coordenadas: ' + e.message);
            return;
        }

        // Enviar los datos al servidor
        axios.post('/api/puertos', portData)
            .then(response => {
                // Crear el polígono del puerto en el mapa
                const coords = convertirCoordenadas(response.data.coordenadas_vertices);
                const polygon = L.polygon(coords, {
                    color: '#0056b3',
                    fillColor: '#007bff',
                    fillOpacity: 0.4,
                    weight: 3
                }).addTo(map);

                // Agregar tooltip
                polygon.bindTooltip(response.data.nombre, {
                    permanent: false,
                    direction: 'top',
                    className: 'map-tooltip'
                });

                // Agregar popup con información detallada
                polygon.bindPopup(createPortPopupContent(response.data));

                // Al abrir el popup, cargar las zonas del puerto
                polygon.on('popupopen', function(e) {
                    loadZonesForPort(response.data.id_puerto);
                });

                polygons.push(polygon);
                portLayers[response.data.id_puerto] = polygon;

                // Limpiar el formulario
                cancelPortCreation();

                // Mostrar mensaje de éxito
                alert('Puerto creado con éxito');
            })
            .catch(error => {
                console.error('Error al crear puerto:', error);
                if (error.response) {
                    alert('Error al crear puerto: ' + (error.response.data.message || error.response.statusText));
                } else {
                    alert('Error al crear puerto: ' + error.message);
                }
            });
    }

    // Función para mostrar el formulario de creación de zona
    function showCreateZoneForm(portId, event) {
        event.stopPropagation();
        document.getElementById(`create-zone-form-${portId}`).style.display = 'block';
    }

    // Función para ocultar el formulario de creación de zona
    function hideCreateZoneForm(portId) {
        document.getElementById(`create-zone-form-${portId}`).style.display = 'none';
    }

    // Función para crear una nueva zona
    function createZone(event, portId) {
        event.preventDefault();
        event.stopPropagation();

        const form = event.target;
        const zoneData = {
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
        event.stopPropagation();
        document.getElementById(`create-space-form-${zoneId}`).style.display = 'block';
    }

    // Función para ocultar el formulario de creación de espacio
    function hideCreateSpaceForm(zoneId) {
        document.getElementById(`create-space-form-${zoneId}`).style.display = 'none';
    }

    // Función para crear un nuevo espacio
    function createSpace(event, zoneId) {
        event.preventDefault();
        event.stopPropagation();

        const form = event.target;
        const spaceData = {
            id_zona: zoneId,
            nombre: form.querySelector('input[type="text"]').value,
            codigo_espacio: form.querySelectorAll('input[type="text"]')[1].value,
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
                console.error('Error al crear espacio:', error);
                if (error.response) {
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
                    `<li><span class="zone-link" onclick="highlightZone(${zona.id_zona}, event)">${zona.nombre}</span> (${zona.codigo_zona})</li>`
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
                    `<li><a href="/espacios/${espacio.id_espacios_contenedores}/contenedores">${espacio.nombre}</a> (${espacio.codigo_espacio})</li>`
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
                                        <a href="/contenedores/${contenedor.id_contenedor}">
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
    function highlightZone(zoneId, event) {
        if (event) event.stopPropagation();

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
        window.location.href = `/espacios/${spaceId}/contenedores`;
    }

    // Función para resaltar un contenedor en el mapa
    function highlightContainer(containerId) {
        window.location.href = `/contenedores/${containerId}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        //Cargamos los puertos y las zonas
        loadPuertos();
        loadZonas();

        // Cargamos en memoria los tipos de contenedores (opcional?)
        axios.get('/api/tipos-contenedor')
            .then(response => {
            })
            .catch(error => {
                console.error('Error al cargar tipos de contenedor:', error);
            });

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

    // Manejar clics en el mapa
    map.on('click', function(e) {
        let isInsidePolygon = false;

        polygons.forEach(polygon => {
            if (polygon.getBounds().contains(e.latlng)) { //Si el click está en un polígono
                isInsidePolygon = true;
            }
        });

        // Si no está dentro de ningún polígono, mostrar coordenada
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
        } else {
            // Mostrar solo coordenadas si está dentro de un polígono
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
