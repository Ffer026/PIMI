<?php

namespace App\Http\Controllers;

use App\Models\Puerto;
use Illuminate\Http\Request;

class PuertoController extends Controller
{
    // Obtener todos los puertos activos
    public function index()
    {
        return response()->json(
            Puerto::where('activo', true)->get()
        );
    }

    // Obtener las zonas de un puerto específico
    public function getZonas($idPuerto)
    {
        $puerto = Puerto::findOrFail($idPuerto);
        return response()->json(
            $puerto->zonas()->where('activa', true)->get()
        );
    }
}