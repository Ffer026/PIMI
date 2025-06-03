<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    // Obtener todas las zonas activas
    public function index()
    {
        return response()->json(
            Zona::where('activa', true)->get()
        );
    }

    // Crear una nueva zona
    public function store(Request $request)
    {
        $request->validate([
            'id_puerto' => 'required|exists:puertos,id_puerto',
            'nombre' => 'required|string|max:100',
            'coordenadas_vertices' => 'required|string',
            'espacios_para_contenedores' => 'required|integer|min:1'
        ]);

        $zona = Zona::create($request->all());

        return response()->json($zona, 201);
    }

    // Obtener los espacios de una zona específica
    public function getEspacios($idZona)
    {
        $zona = Zona::findOrFail($idZona);
        return response()->json(
            $zona->espacios()->where('activa', true)->get()
        );
    }
}