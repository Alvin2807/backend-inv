<?php

namespace App\Http\Controllers\Api;

use App\Models\TipoEntrada;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoEntradasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mostrar tipos de entradas
        $tipoEntrada = TipoEntrada::all();
        return response()->json([
            "ok"=> true,
            "data"=>$tipoEntrada
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function entradaPorAlmacen()
    {
        $tipoEntrada = TipoEntrada::
        select('id_tipo_entrada','tipo_entrada')
        ->where('tipo_entrada','NÚMERO DE TRANS')
        ->first();
        return response()->json([
            "ok"=> true,
            "data"=>$tipoEntrada
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoEntrada $tipoEntrada)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoEntrada $tipoEntrada)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoEntrada $tipoEntrada)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoEntrada $tipoEntrada)
    {
        //
    }

    public function elegirTipoEntrada($id_despacho){
        $tipoEntrada = TipoEntrada::
        select('id_tipo_entrada','tipo_entrada','fk_despacho')
        ->where('fk_despacho', $id_despacho)
        ->first();
        return response()->json([
            "ok"=> true,
            "data"=>$tipoEntrada
        ]);
    }
}
