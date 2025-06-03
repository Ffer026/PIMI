<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ZonaController extends Controller
{
    /**
     * Obtener todas las zonas
     */
    public function index()
    {
        $zonas = DB::table('zonas')->get();
        return response()->json($zonas);
    }

    /**
     * Crear una nueva zona
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_puerto' => 'required|exists:puertos,id_puerto',
            'nombre' => 'required|string',
            'codigo_zona' => 'nullable|string',
            'coordenadas_vertices' => 'required|string',
            'espacios_para_contenedores' => 'required|integer|min:1',
            'max_niveles_apilamiento' => 'nullable|integer|min:1',
            'observaciones' => 'nullable|string',
            'activa' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();
        $id = DB::table('zonas')->insertGetId([
            ...$data,
            'fecha_creacion' => now(),
            'ultima_actualizacion' => now()
        ]);

        $zona = DB::table('zonas')->find($id);
        return response()->json($zona, 201);
    }

    /**
     * Obtener espacios de una zona específica
     */
    public function getEspacios($id)
    {
        $espacios = DB::table('espacios_contenedores')->where('id_zona', $id)->get();
        return response()->json($espacios);
    }
}