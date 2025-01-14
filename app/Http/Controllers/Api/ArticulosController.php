<?php

namespace App\Http\Controllers\Api;

use App\Models\Articulo;
use App\Http\Controllers\Controller;
use App\Models\VistaArticulos;
use Illuminate\Http\Request;
use App\Http\Requests\Articulos\RegistrarRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Articulos\EditarRequest;
use Carbon\Carbon;
class ArticulosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mostrar articulos
        $articulo = VistaArticulos::all();
        return response()->json([
            "ok"=>true,
            "data"=>$articulo
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function editarArticulo(EditarRequest $request)
    {
        //Editar articulos
        try {
            DB::beginTransaction();
            $codigo    = strtoupper($request->input('codigo'));
            $id_articulo = (int)$request->input('id_articulo');
            $consulta  = Articulo::
            select('id_articulo','codigo')
            ->where('codigo', $codigo)
            ->where('id_articulo', '<>', $id_articulo)
            ->get();
            if (count($consulta) > 0) {
                return response()->json([
                    "ok" =>true,
                    "existe" =>'Ya existe un insumo con el código '.$codigo
                ]);
            } else {
                $articulo = new Articulo();
                $data['fk_marca']        = $request->input('fk_marca');
                $data['fk_modelo']       = $request->input('fk_modelo');
                $data['fk_categoria']    = $request->input('fk_categoria');
                $data['fk_color']        = $request->input('fk_color');
                $data['referencia']      = strtoupper($request->input('referencia'));
                $data['codigo']          = $codigo;
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $data['fecha_modifica']   = Carbon::now()->format('Y-m-d H:s:i');
                $articulo = Articulo::where('id_articulo', $id_articulo)->update($data);
                DB::commit();
                return response()->json([
                    "ok"=>true,
                    "data"=>$articulo,
                    "exitoso"=>'Se guardo satisfactoriamente'
                ]);
            }
        } catch (\Exception $th) {
           DB::rollBack();
           return response()->json([
            "ok" =>false,
            "data"=>$th->getMessage(),
            "errorArticuloEditar"=>'Hubo un error consulte con el Administrador del sistema'
           ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegistrarRequest $request)
    {
        //Registrar artículo
        try {
            DB::beginTransaction();
            $codigo = strtoupper($request->input('codigo'));
            $consulta = Articulo::
            select('id_articulo','codigo')
            ->where('codigo', $codigo)
            ->get();
            if (count($consulta) > 0) {
             return response()->json([
                 "ok" =>true,
                 "existeArticulo" =>'Ya existe un artículo con el código '.$codigo
             ]);
            } else {
             $articulo = new Articulo();
             $articulo->referencia = strtoupper($request->input('referencia'));
             $articulo->fk_marca = $request->input('fk_marca');
             $articulo->fk_modelo = $request->input('fk_modelo');
             $articulo->fk_categoria = $request->input('fk_categoria');
             $articulo->fk_color = $request->input('fk_color');
             $articulo->codigo = $codigo;
             $articulo->usuario_crea = strtoupper($request->input('usuario'));
             $articulo->save();
             DB::commit();
             return response()->json([
                 "ok" =>true,
                 "data"=>$articulo,
                 "exitosoArticulo"=>'Se guardo satisfactoriamente'
             ]);
            }
 
         } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
             "ok" =>false,
             "data"=>$th->getMessage(),
             "errorArticulo"=>'Hubo un error consulte con el Administrador del sistema'
            ]);
         }
    }

    /**
     * Display the specified resource.
     */
    public function show(Articulo $articulo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articulo $articulo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Articulo $articulo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articulo $articulo)
    {
        //
    }
}
