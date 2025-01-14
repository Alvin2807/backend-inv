<?php

namespace App\Http\Controllers\Api;

use App\Models\Solicitud;
use App\Http\Controllers\Controller;
use App\Models\VistaSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Solicitud\StoreRequest;
use App\Models\Articulo;
use App\Models\Detalle;
use App\Utils\Utilidades;

class SolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mostrar solicitudes pendientes
        $solicitud = VistaSolicitud::
        select('id_solicitud','fk_tipo_solicitud','tipo_solicitud','fk_despacho','no_entrada','fecha_entrada','no_salida','fecha_salida',
        'incidencia','cantidad_solicitada','cantidad_pendiente','cantidad_confirmada','despacho','estado')
        ->where('estado','Pendiente')
        ->get();
        return response()->json([
            "ok"=>true,
            "data"=>$solicitud,
            "Pendientes"=>count($solicitud)
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
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $tipo_accion = strtoupper($request->input('tipo_accion'));
            if ($tipo_accion == 'ENTRADA') {
                $solicitud = new Solicitud();
                $solicitud->fk_despacho    = $request->input('fk_despacho');
                $solicitud->no_entrada     = strtoupper($request->input('no_entrada'));
                $solicitud->fk_tipo_solicitud = $request->input('fk_tipo_solicitud');
                $solicitud->fecha_entrada  = Utilidades::formatoFecha($request->input('fecha_entrada'));
                $solicitud->usuario_crea   = strtoupper($request->input('usuario'));
                $solicitud->save();

                $items = $request->input('detalles');
                for ($i=0; $i <count($items) ; $i++) { 
                    $detalle = new Detalle();
                    $detalle->fk_tipo_solicitud =  $solicitud->fk_tipo_solicitud;
                    $detalle->fk_articulo = $items[$i]['fk_articulo'];
                    $detalle->fk_solicitud = $solicitud->id;
                    $detalle->cantidad_solicitada = $items[$i]['cantidad_solicitada'];
                    $detalle->cantidad_pendiente  = $items[$i]['cantidad_solicitada'];
                    $detalle->usuario_crea = $solicitud->usuario_crea;
                    $detalle->save();

                    $dataSolicitudCantidad = new Solicitud();
                    $data['cantidad_solicitada'] = $this->sumarCantidadSolicitada($solicitud->id);
                    $data['cantidad_confirmada'] = $this->sumarCantidadConfirmada($solicitud->id);
                    $data['cantidad_pendiente']  = $this->sumarCantidadPendiente($solicitud->id);
                    $dataAccionCantidad = Solicitud::where('id_solicitud', $solicitud->id)->update($data);
    
                    $consultarArticulo = Articulo::
                    select('id_articulo','cantidad_pedida')
                    ->where('id_articulo', $items[$i]['fk_articulo'])
                    ->get();
                    if (count($consultarArticulo) > 0) {
                        $actualizarArticulo = new Articulo();
                        $dataArticulo['cantidad_pedida'] = $consultarArticulo[0]['cantidad_pedida'] + $items[$i]['cantidad_solicitada'];
                        $actualizarArticulo = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($dataArticulo);
                    }
                }

                DB::commit();
                return response()->json([
                  "ok"=>true,
                  "data"=>$solicitud,
                  "exitoso"=>'Se guardo satisfactoriamente'
                ]);
            }
         
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok"=>false,
                "data"=>$th->getMessage(),
                "errorRegistro"=>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }
    }

    public function sumarCantidadSolicitada($id_solicitud)
    {
        $cantidad_solicitada = Detalle::
        select('id_detalle','cantidad_solicitada')
        ->where('fk_solicitud', $id_solicitud)
        ->sum('cantidad_solicitada');
        return $cantidad_solicitada;
    }

    public function sumarCantidadConfirmada($id_solicitud)
    {
        $cantidad_confirmada = Detalle::
        select('id_detalle','cantidad_confirmada')
        ->where('fk_solicitud', $id_solicitud)
        ->sum('cantidad_confirmada');
        return $cantidad_confirmada;
    }


    public function sumarCantidadPendiente($id_solicitud)
    {
        $cantidad_pendiente = Detalle::
        select('id_detalle','cantidad_pendiente')
        ->where('fk_solicitud', $id_solicitud)
        ->sum('cantidad_pendiente');
        return $cantidad_pendiente;
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solicitud $solicitud)
    {
        //
    }
}
