<?php

namespace App\Http\Controllers;

use App\Models\Contenedor;
use Illuminate\Http\Request;

class ContenedorController extends Controller
{
    public function index($espacioId)
    {
        $contenedores = Contenedor::with(['tipoContenedor', 'espacioContenedor'])
            ->where('espacio_contenedor_id', $espacioId)
            ->paginate(10); // Puedes ajustar el número de items por página

        return view('contenedores.index', compact('contenedores', 'espacioId'));
    }
}