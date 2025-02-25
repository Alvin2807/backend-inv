<?php

namespace App\Http\Controllers\Api;

use App\Models\Modelo;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ModelosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mostrar modelos
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Modelo $modelo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Modelo $modelo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modelo $modelo)
    {
        //
    }

    public function mostrarModeloMarca($id_marca){
        $modelo = Modelo::
        select('id_modelo','fk_marca','modelo')
        ->where('fk_marca', [$id_marca])
        ->orderBy('modelo','asc')
        ->get();
        return response()->json([
            "ok"=>true,
            "data"=>$modelo
        ]);
    }
}
