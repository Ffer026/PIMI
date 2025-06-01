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

Route::get('/api/puertos/{id}/zonas', function ($id) {
    $zonas = DB::table('zonas')->where('id_puerto', $id)->get();
    return response()->json($zonas);
});

// Zona routes
Route::get('/api/zonas', function (Request $request) {
    $datos = DB::table('zonas')->get();
    return response()->json($datos);
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

// Tipo contenedor routes
Route::get('/api/tipos-contenedor', function () {
    $tipos = DB::table('tipos_contenedor')->get();
    return response()->json($tipos);
});