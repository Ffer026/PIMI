<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuertoController extends Controller
{
    /**
     * Obtener todos los puertos
     */
    public function index()
    {
        $puertos = DB::table('puertos')->get();
        return response()->json($puertos);
    }

    /**
     * Obtener zonas de un puerto específico
     */
    public function getZonas($id)
    {
        $zonas = DB::table('zonas')->where('id_puerto', $id)->get();
        return response()->json($zonas);
    }
}