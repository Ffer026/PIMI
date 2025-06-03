<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
//new
use App\Http\Controllers\PuertoController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\ContenedorController;

use Illuminate\Support\Facades\DB;

// Rutas para Puertos
Route::get('/puertos', [PuertoController::class, 'index']); // Obtener todos los puertos
Route::get('/puertos/{id}/zonas', [PuertoController::class, 'getZonas']); // Obtener zonas de un puerto

// Rutas para Zonas
Route::get('/zonas', [ZonaController::class, 'index']); // Obtener todas las zonas
Route::post('/zonas', [ZonaController::class, 'store']); // Crear una nueva zona
Route::get('/zonas/{id}/espacios', [ZonaController::class, 'getEspacios']); // Obtener espacios de una zona

// Contenedor routes
Route::post('/api/contenedores', function (Request $request) {
    $data = $request->validate([
        'tipo_contenedor_iso' => 'required|exists:tipos_contenedor,iso_code',
        'espacio_contenedor_id' => 'required|exists:espacios_contenedores,id_espacios_contenedores',
        'propietario' => 'required|string|max:100',
        'material_peligroso' => 'boolean'
    ]);
    
    $contenedor = \App\Models\Contenedor::create($data);
    return response()->json($contenedor, 201);
});

Route::delete('/api/contenedores/{id}', function ($id) {
    $contenedor = \App\Models\Contenedor::findOrFail($id);
    $contenedor->delete();
    return response()->json(['message' => 'Contenedor eliminado correctamente'], 200);
});

Route::get('/', function () {
    return view('welcome');
});

// Puerto routes
Route::get('/api/puertos', function (Request $request) {
    $datos = DB::table('puertos')->get();
    return response()->json($datos);
});

Route::post('/api/puertos', function (Request $request) {
    $data = $request->validate([
        'nombre' => 'required|string',
        'codigo' => 'required|string|unique:puertos',
        'coordenadas_vertices' => 'required|string',
        'descripcion' => 'nullable|string',
        'direccion' => 'nullable|string',
        'telefono_contacto' => 'nullable|string',
        'activo' => 'boolean'
    ]);
    
    $id = DB::table('puertos')->insertGetId([
        ...$data,
        'fecha_creacion' => now(),
        'ultima_actualizacion' => now()
    ]);
    
    $puerto = DB::table('puertos')->find($id);
    return response()->json($puerto, 201);
});

Route::get('/api/puertos/{id}/zonas', function ($id) {
    $zonas = DB::table('zonas')->where('id_puerto', $id)->get();
    return response()->json($zonas);
});

// Zona routes
Route::get('/api/zonas', function (Request $request) {
    $datos = DB::table('zonas')->get();
    return response()->json($datos);
});

Route::post('/api/zonas', function (Request $request) {
    $data = $request->validate([
        'id_puerto' => 'required|exists:puertos,id_puerto',
        'nombre' => 'required|string',
        'codigo_zona' => 'nullable|string',
        'coordenadas_vertices' => 'required|string',
        'espacios_para_contenedores' => 'required|integer|min:1',
        'max_niveles_apilamiento' => 'nullable|integer|min:1',
        'observaciones' => 'nullable|string',
        'activa' => 'boolean'
    ]);
    
    $id = DB::table('zonas')->insertGetId([
        ...$data,
        'fecha_creacion' => now(),
        'ultima_actualizacion' => now()
    ]);
    
    $zona = DB::table('zonas')->where('id_zona', $id)->first();
    return response()->json($zona, 201);
});

Route::get('/api/zonas/{id}/espacios', function ($id) {
    $espacios = DB::table('espacios_contenedores')->where('id_zona', $id)->get();
    return response()->json($espacios);
});

// Espacio routes
Route::get('/api/espacios/{id}/contenedores', function ($id) {
    $contenedores = \App\Models\Contenedor::where('espacio_contenedor_id', $id)
                        ->paginate(15); // Usamos paginate en lugar de get para la paginación
    
    return view('index', [ // Reemplaza 'tu_vista' con el nombre real de tu vista blade
        'contenedores' => $contenedores,
        'espacioId' => $id
    ]);
});

Route::post('/api/espacios', function (Request $request) {
    $data = $request->validate([
        'id_zona' => 'required|exists:zonas,id_zona', // Aseguramos que la zona exista
        'nombre' => 'required|string|max:100',        // Máximo 100 caracteres (como en la migración)
        'codigo_espacio' => 'nullable|string|max:10', // Máximo 10 caracteres y nullable
        'observaciones' => 'nullable|string',
        'activa' => 'boolean'
    ]);
    
    // Insertamos el espacio y obtenemos el ID de la PK generada
    $id = DB::table('espacios_contenedores')->insertGetId([
        ...$data,
        'ultima_actualizacion' => now() // La migración usa timestamp automático, pero lo incluimos por claridad
    ]);
    
    // Recuperamos el espacio recién creado usando la PK correcta (id_espacios_contenedores)
    $espacio = DB::table('espacios_contenedores')
        ->where('id_espacios_contenedores', $id)
        ->first();
    
    return response()->json($espacio, 201);
});

// Tipo contenedor routes
Route::get('/api/tipos-contenedor', function () {
    $tipos = DB::table('tipos_contenedor')->get();
    return response()->json($tipos);
});