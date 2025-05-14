<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/puertos', function (Request $request) {
    $datos = DB::table('puertos')->get();
    return response()->json($datos);
});

Route::get('/api/zonas', function (Request $request) {
    $datos = DB::table('zonas')->get();
    return response()->json($datos);
});