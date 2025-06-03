<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenedores en Espacio {{ $espacioId }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Contenedores en Espacio ID: {{ $espacioId }}</h1>
        
        <!-- Formulario para añadir nuevo contenedor -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Añadir Nuevo Contenedor</h5>
            </div>
            <div class="card-body">
                <form id="addContenedorForm">
                    @csrf
                    <input type="hidden" name="espacio_contenedor_id" value="{{ $espacioId }}">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tipo_contenedor_iso" class="form-label">Tipo de Contenedor</label>
                                <select class="form-select" id="tipo_contenedor_iso" name="tipo_contenedor_iso" required>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach(\App\Models\TipoContenedor::all() as $tipo)
                                        <option value="{{ $tipo->iso_code }}">{{ $tipo->iso_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="propietario" class="form-label">Propietario</label>
                                <input type="text" class="form-control" id="propietario" name="propietario" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Material Peligroso</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="material_peligroso" name="material_peligroso" value="1">
                                    <label class="form-check-label" for="material_peligroso">
                                        Sí
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Añadir Contenedor</button>
                </form>
            </div>
        </div>
        
        <!-- Lista de contenedores -->
        @if($contenedores->isEmpty())
            <div class="alert alert-info">
                No hay contenedores en este espacio.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tipo Contenedor</th>
                            <th>Propietario</th>
                            <th>Material Peligroso</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contenedores as $contenedor)
                            <tr id="contenedor-{{ $contenedor->id }}">
                                <td>{{ $contenedor->id }}</td>
                                <td>{{ $contenedor->tipo_contenedor_iso }}</td>
                                <td>{{ $contenedor->propietario }}</td>
                                <td>
                                    @if($contenedor->material_peligroso)
                                        <span class="badge bg-danger">Sí</span>
                                    @else
                                        <span class="badge bg-success">No</span>
                                    @endif
                                </td>
                                <td>{{ $contenedor->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-contenedor" 
                                            data-id="{{ $contenedor->id }}">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $contenedores->links() }}
            </div>
        @endif
        
        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Volver</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Manejar el envío del formulario
            $('#addContenedorForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '/api/contenedores',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Recargar la página para ver el nuevo contenedor
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('Error al añadir el contenedor: ' + xhr.responseJSON.message);
                    }
                });
            });
            
            // Manejar la eliminación de contenedores
            $('.delete-contenedor').on('click', function() {
                if (!confirm('¿Está seguro de que desea eliminar este contenedor?')) {
                    return;
                }
                
                const contenedorId = $(this).data('id');
                
                $.ajax({
                    url: `/api/contenedores/${contenedorId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Eliminar la fila de la tabla
                        $(`#contenedor-${contenedorId}`).remove();
                        
                        // Mostrar mensaje de éxito
                        alert('Contenedor eliminado correctamente');
                    },
                    error: function(xhr) {
                        alert('Error al eliminar el contenedor: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>
</html>