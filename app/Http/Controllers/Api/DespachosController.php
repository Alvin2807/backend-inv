<?php

namespace App\Http\Controllers\Api;

use App\Models\VistaDespachos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DespachosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(VistaDespachos $vistaDespachos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VistaDespachos $vistaDespachos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VistaDespachos $vistaDespachos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VistaDespachos $vistaDespachos)
    {
        //
    }

    public function mostrarDespachosporEntrada(){
        $vistaDespachos = VistaDespachos::
        select('id_despacho','despacho')
        ->where('provincia', 'PANAMÁ')
        ->get();
        return response()->json([
            "ok"=>true,
            "data"=>$vistaDespachos
        ]);
    }
}
