<?php

namespace App\Http\Controllers\Api;

use App\Models\Solicitud;
use App\Http\Controllers\Controller;
use App\Models\VistaSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Solicitud\StoreRequest;
use App\Http\Requests\Solicitud\EditarSolicitudRequest;
use App\Http\Requests\Solicitud\EliminarArticuloRequest;
use App\Models\Articulo;
use App\Models\Detalle;
use App\Utils\Utilidades;
use Carbon\Carbon;
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
                $solicitud->fk_tipo_solicitud = $request->input('fk_tipo_solicitud');
                $solicitud->fecha_entrada  = Utilidades::formatoFecha($request->input('fecha_entrada'));
                $solicitud->usuario_crea   = strtoupper($request->input('usuario'));
                $solicitud->save();

                $items = $request->input('detalles');
                for ($i=0; $i <count($items) ; $i++) { 
                    $detalle = new Detalle();
                    $detalle->no_item = $items[$i]['no_item'];
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

    public function sumarCantidadPedidaArticulo($id_articulo)
    {
        $cantidad_pedida = Articulo::
        select('id_articulo','cantidad_pedida')
        ->where('id_articulo', $id_articulo)
        ->sum('cantidad_pedida');
        return $cantidad_pedida;
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

    public function editarSolicitud(EditarSolicitudRequest $request){
        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('fk_solicitud');
            $fk_tipo_solicitud = $request->input('fk_tipo_solicitud');
            $validar  = Solicitud::
            where('id_solicitud', $id_solicitud)
            ->where('estado', 'Pendiente')
            ->count();
            if ($validar) {
                $data['fk_despacho']    = $request->input('fk_despacho');
                $data['fecha_entrada']  = Utilidades::formatoFecha($request->input('fecha_entrada'));
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $data['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
                $solicitud = Solicitud::where('id_solicitud', $id_solicitud)->update($data);

                $items = $request->input('detalles');
                for ($i=0; $i <count($items) ; $i++) { 
                    if (isset($items[$i]['id_detalle'])) {
                        $detalle = new Detalle();
                        $consultarDetalle = Detalle::
                        select('id_detalle','cantidad_solicitada')
                        ->where('fk_articulo', $items[$i]['fk_articulo'])
                        ->where('fk_solicitud', $id_solicitud)
                        ->get();

                        $detalleData['fk_articulo'] = $items[$i]['fk_articulo'];
                        $detalleData['cantidad_solicitada'] = $consultarDetalle[0]['cantidad_solicitada'] - $consultarDetalle[0]['cantidad_solicitada'] + $items[$i]['cantidad_solicitada'];
                        $detalleData['cantidad_pendiente']  = $detalleData['cantidad_solicitada'];
                        $detalleData['usuario_modifica']    = $data['usuario_modifica'];
                        $detalleData['fecha_modifica']      = $data['fecha_modifica'];
                        $detallesSolicitud = Detalle::where('id_detalle', $items[$i]['id_detalle'])->update($detalleData);

                        $dataSolicitudCantidad = new Solicitud();
                        $dataSolicitud['cantidad_solicitada'] = $this->sumarCantidadSolicitada($id_solicitud);
                        $dataSolicitud['cantidad_pendiente']  = $this->sumarCantidadPendiente($id_solicitud);
                        $dataSolicitudCantidad = Solicitud::where('id_solicitud', $id_solicitud)->update($dataSolicitud);

                        $consultarArticuloExite = Articulo::
                        select('id_articulo','cantidad_pedida')
                        ->where('id_articulo', $items[$i]['fk_articulo'])
                        ->get();
                        if (count($consultarArticuloExite) > 0) {
                            $actualizarArticuloExite = new Articulo();
                            $dataArticuloExiste['cantidad_pedida'] = $consultarArticuloExite[0]['cantidad_pedida'] - $consultarDetalle[0]['cantidad_solicitada'] + $items[$i]['cantidad_solicitada'];
                            $actualizarArticuloExite = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($dataArticuloExiste);
                        } 

                    } else {
                        $detalleNuevo = new Detalle();
                        $detalleNuevo->no_item = $items[$i]['no_item'];
                        $detalleNuevo->fk_tipo_solicitud = $fk_tipo_solicitud;
                        $detalleNuevo->fk_solicitud = $id_solicitud;
                        $detalleNuevo->fk_articulo = $items[$i]['fk_articulo'];
                        $detalleNuevo->cantidad_solicitada = $items[$i]['cantidad_solicitada'];
                        $detalleNuevo->cantidad_pendiente =  $detalleNuevo->cantidad_solicitada;
                        $detalleNuevo->usuario_crea = $data['usuario_modifica'];
                        $detalleNuevo->save();

                        $actualizarCantidad = new Solicitud();
                        $dataCantidad['cantidad_solicitada'] = $this->sumarCantidadSolicitada($id_solicitud);
                        $dataCantidad['cantidad_pendiente']  = $this->sumarCantidadPendiente($id_solicitud);
                        $actualizarCantidad = Solicitud::where('id_solicitud', $id_solicitud)->update($dataCantidad);

                        $consultarArticuloActualizar = Articulo::
                        select('id_articulo','cantidad_pedida')
                        ->where('id_articulo', $items[$i]['fk_articulo'])
                        ->get();
                        if (count($consultarArticuloActualizar) > 0) {
                            $actualizarArticuloData = new Articulo();
                            $dataArticuloCantidad['cantidad_pedida'] =  $consultarArticuloActualizar[0]['cantidad_pedida'] + $items[$i]['cantidad_solicitada'];
                            $actualizarArticuloData = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($dataArticuloCantidad);
                        }
                        }
                }

            DB::commit();
               return response()->json([
                "data" =>true,
                "ok" =>$solicitud,
                "exitoso" =>'Se guardo satisfactoriamente'
               ]);
            }
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data"=>$th->getMessage(),
                "errorRegistro" =>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }

    }

    public function eliminarArticuloSolicitud(EliminarArticuloRequest $request){
        try {
           DB::beginTransaction();
           $id_detalle    = $request->input('id_detalle');
           $fk_solicitud  = $request->input('fk_solicitud');
           $fk_articulo   = $request->input('fk_articulo');
           $usuario       = strtoupper($request->input('usuario'));
           $cantidad_solicitada = $request->input('cantidad_solicitada');
           $consulta   = Detalle::
           select('id_detalle','fk_articulo','cantidad_solicitada')
           ->where('id_detalle',  $id_detalle)
           ->where('fk_articulo', $fk_articulo)
           ->where('cantidad_confirmada', '>', 0)
           ->get();
           if (count($consulta) > 0) {
             return 'No se puede eliminar este artículo';
           } else {
            $detalles = new Detalle();
            $detalles = Detalle::where('id_detalle',$id_detalle)->delete();

            $solicitudEliminar = new Solicitud();
            $solicitudData['cantidad_solicitada'] = $this->sumarCantidadSolicitada($fk_solicitud);
            $solicitudData['cantidad_pendiente']  = $this->sumarCantidadPendiente($fk_solicitud);
            $solicitudData['usuario_modifica']    = $usuario;
            $solicitudData['fecha_modifica']      = Carbon::now()->format('Y-m-d H:i:s');
            $solicitudEliminar = Solicitud::where('id_solicitud', $fk_solicitud)->update($solicitudData);

            $articuloEliminar = new Articulo();
            $articuloData['cantidad_pedida']  = $this->sumarCantidadPedidaArticulo($fk_articulo) - $cantidad_solicitada;
            $articuloData['usuario_modifica'] = $usuario;
            $articuloData['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
            $articuloEliminar = Articulo::where('id_articulo', $fk_articulo)->update($articuloData);
            DB::commit();
            return response()->json([
                "ok" =>true,
                "data"=>$detalles,
                "eliminadoArticulo" =>'Se eliminó satisfactoriamente'
            ]);
           }
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data"=>$th->getMessage(),
                "errorEliminarArticulo" =>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }
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
