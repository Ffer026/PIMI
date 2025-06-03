<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use Illuminate\Http\Request;

class EspacioController extends Controller
{
    // Crear un nuevo espacio
    public function store(Request $request)
    {
        $request->validate([
            'id_zona' => 'required|exists:zonas,id_zona',
            'nombre' => 'required|string|max:100'
        ]);

        $espacio = Espacio::create($request->all());

        return response()->json($espacio, 201);
    }
}