<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any?}', function () {
    return view('app'); // Devuelve la plantilla de Angular
})->where('any', '.*');

Route::get('/data', function () {
    return response()->json(['message' => 'Hola desde Laravel!']);
});