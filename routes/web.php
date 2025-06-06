<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/', function () { //Página de inicio
    return view('welcome');
});

// Rutas para Puertos
Route::get('/api/puertos', function (Request $request) { // Devuelve todos los puertos en JSON
    $datos = DB::table('puertos')->get();
    return response()->json($datos);
});
Route::get('/api/puertos/{id}/zonas', function ($id) {
    $zonas = DB::table('zonas')->where('id_puerto', $id)->get();
    return response()->json($zonas);
});


// Rutas para Zonas
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
    
    //Genera el ID y lo inserta en la tabla 'zonas'
    $id = DB::table('zonas')->insertGetId([
        ...$data,
        'fecha_creacion' => now(),
        'ultima_actualizacion' => now()
    ]);
    
    $zona = DB::table('zonas')->where('id_zona', $id)->first(); //Recupera la zona
    return response()->json($zona, 201); // Devuelve la zona con código 201 creado
});
Route::get('/api/zonas/{id}/espacios', function ($id) {
    $espacios = DB::table('espacios_contenedores')->where('id_zona', $id)->get();
    return response()->json($espacios);
});

// Rutas para contenedores
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

// Rutas para Espacios
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