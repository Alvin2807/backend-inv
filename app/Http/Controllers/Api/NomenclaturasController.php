<?php

namespace App\Http\Controllers\Api;

use App\Models\Nomenclatura;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Nomenclaturas\RegistrarRequest;
use App\Models\VistaNomenclaturas;

class NomenclaturasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mostrar nomenclaturas
        $nomenclatura = VistaNomenclaturas::all();
        return response()->json([
            "ok" =>true,
            "data" =>$nomenclatura
        ]);
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
    public function store(RegistrarRequest $request)
    {
        //Registrar Nomenclatura
        try {
            DB::beginTransaction();
            $nomenclatura = strtoupper($request->input('nomenclatura'));
            $fk_despacho  = $request->input('fk_despacho');
            $usuario      = strtoupper($request->input('usuario'));
            $consulta     = Nomenclatura::
            select('id_nomenclatura','nomenclatura')
            ->where('nomenclatura', $nomenclatura)
            ->get();
            if (count($consulta) > 0) {
                return response()->json([
                    "ok" =>true,
                    "existe"=>'Ya existe una nomenclatura '.$nomenclatura
                ]);
            } else {
                $nomenclaturas = new Nomenclatura();
                $nomenclaturas->nomenclatura = $nomenclatura;
                $nomenclaturas->fk_despacho  = $fk_despacho;
                $nomenclaturas->usuario_crea = $usuario;
                $nomenclaturas->save();
    
                DB::commit();
                return response()->json([
                    "ok" =>true,
                    "data"=>$nomenclaturas,
                    "exitoso"=>'Se guardo satisfactoriamente'
                ]);
            }
    
           } catch (\Exception $th) {
                DB::rollBack();
                return response()->json([
                    "ok" =>false,
                    "data"=>$th->getMessage(),
                    "errorRegistro"=>'Hubo un error consulte con el Administrador del sisetema'
                ]);
           }    
    }

    /**
     * Display the specified resource.
     */
    public function show(Nomenclatura $nomenclatura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nomenclatura $nomenclatura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nomenclatura $nomenclatura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nomenclatura $nomenclatura)
    {
        //
    }
}
