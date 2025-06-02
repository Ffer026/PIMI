<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        'id_puerto' => 'required|exists:puertos,id',
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
    
    $zona = DB::table('zonas')->find($id);
    return response()->json($zona, 201);
});

Route::get('/api/zonas/{id}/espacios', function ($id) {
    $espacios = DB::table('espacios_contenedores')->where('id_zona', $id)->get();
    return response()->json($espacios);
});

// Espacio routes
Route::get('/api/espacios/{id}/contenedores', function ($id) {
    $contenedores = DB::table('contenedores')->where('id_espacio_contenedor', $id)->get();
    return response()->json($contenedores);
});

Route::post('/api/espacios', function (Request $request) {
    $data = $request->validate([
        'id_zona' => 'required|exists:zonas,id',
        'nombre' => 'required|string',
        'codigo_espacio' => 'nullable|string',
        'observaciones' => 'nullable|string',
        'activa' => 'boolean'
    ]);
    
    $id = DB::table('espacios_contenedores')->insertGetId([
        ...$data,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    $espacio = DB::table('espacios_contenedores')->find($id);
    return response()->json($espacio, 201);
});

// Tipo contenedor routes
Route::get('/api/tipos-contenedor', function () {
    $tipos = DB::table('tipos_contenedor')->get();
    return response()->json($tipos);
});