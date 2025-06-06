<?php

namespace App\Http\Controllers;

use App\Models\Contenedor;
use App\Models\TipoContenedor;
use Illuminate\Http\Request;

class ContenedorController extends Controller
{
    public function index($espacioId)
    {
        $contenedores = Contenedor::where('espacio_contenedor_id', $espacioId)
            ->paginate(10); // Ajusta el número según necesites
            
        return view('nombre_de_tu_vista', [
            'espacioId' => $espacioId,
            'contenedores' => $contenedores
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_contenedor_iso' => 'required|exists:tipos_contenedor,iso_code',
            'propietario' => 'required|string|max:255',
            'material_peligroso' => 'nullable|boolean',
            'espacio_contenedor_id' => 'required|exists:espacios,id'
        ]);

        $contenedor = Contenedor::create($request->all());

        return response()->json($contenedor, 201);
    }

    public function destroy(Contenedor $contenedor)
    {
        $contenedor->delete();
        
        return response()->json(['message' => 'Contenedor eliminado correctamente']);
    }
}