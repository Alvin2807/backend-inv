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
use App\Http\Requests\Solicitud\EliminartSolicitudRequest;
use App\Http\Requests\Solicitud\ConfirmarSolicitudRequest;
use App\Http\Requests\Solicitud\SalidaRegistrarRequest;
use App\Http\Requests\Solicitud\EditarSaalidaRequest;
use App\Http\Requests\Solicitud\EliminarSalidaRequest;
use App\Http\Requests\Solicitud\ConfirmarSalidaRequest;
use App\Models\Articulo;
use App\Models\Detalle;
use App\Models\VistaArticulosEntrada;
use App\Models\VistaDetalle;
use App\Utils\Utilidades;
use Carbon\Carbon;

use function Laravel\Prompts\form;

class SolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mostrar solicitudes pendientes
        $solicitud = VistaSolicitud::
        select('id_solicitud','fk_tipo_solicitud','tipo_solicitud','fk_despacho','fecha_entrada','fecha_salida',
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
                $solicitud->preparado_por = strtoupper($request->input('preparado_por'));
                $solicitud->num_solicitud = $request->input('num_solicitud');
                $solicitud->fecha_entrada  = Utilidades::formatoFecha($request->input('fecha_entrada'));
                $solicitud->usuario_crea   = strtoupper($request->input('usuario'));
                $solicitud->save();

                $items = $request->input('articulos');
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

    public function sumarCantidadStockArticulo($id_articulo)
    {
        $cantidad_stock = Articulo::
        select('id_articulo','stock')
        ->where('id_articulo', $id_articulo)
        ->sum('stock');
        return $cantidad_stock;
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
            $id_solicitud = $request->input('id_solicitud');
            $fk_tipo_solicitud = $request->input('fk_tipo_solicitud');
            $validar  = Solicitud::
            where('id_solicitud', $id_solicitud)
            ->where('estado', 'Pendiente')
            ->count();
            if ($validar) {
                $data['fk_despacho']    = $request->input('fk_despacho');
                $data['fecha_entrada']  = Utilidades::formatoFecha($request->input('fecha_entrada'));
                $data['usuario_modifica'] = strtoupper($request->input('usuario'));
                $data['num_solicitud'] = $request->input('num_solicitud');
                $data['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
                $solicitud = Solicitud::where('id_solicitud', $id_solicitud)->update($data);

                $items = $request->input('articulos');
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

    public function eliminarSolicitud(EliminartSolicitudRequest $request){
        try {
           DB::beginTransaction();
           $id_solicitud = $request->input('id_solicitud');
           $usuario      = strtoupper($request->input('usuario'));
           $solicitudes = new Solicitud();
           $consultaSolicitud = Solicitud::
           select('id_solicitud','cantidad_confirmada')
           ->where('id_solicitud',$id_solicitud)
           ->where('cantidad_confirmada', '>', 0)
           ->get();
           if (count($consultaSolicitud) > 0) {
            return 'No se puede eliminar esta solicitud';
           } else {
            $items = $request->input('detalles');
            for ($i=0; $i <count($items) ; $i++) { 
                $detalle = new Detalle();
                $detalle = Detalle::where('id_detalle', $items[$i]['id_detalle'])->delete();

                $solicitud = New Solicitud();
                $solicitud = Solicitud::where('id_solicitud', $id_solicitud)->delete();

                $articulo = New Articulo();
                $articuloDataSolicitud['cantidad_pedida']  = $this->sumarCantidadPedidaArticulo($items[$i]['fk_articulo']) - $items[$i]['cantidad_solicitada'];
                $articuloDataSolicitud['usuario_modifica'] = $usuario;
                $articuloDataSolicitud['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
                $articulo = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($articuloDataSolicitud);
           }

        }
           DB::commit();
           return response()->json([
            "ok" =>true,
            "data"=>$solicitudes,
            "eliminarSolicitud" =>'Se eliminó satisfactoriamente'
        ]);

        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data"=>$th->getMessage(),
                "errorEliminarSolicitud" =>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }
    }

    public function confirmarSolicitud(ConfirmarSolicitudRequest $request){
        try {
            DB::beginTransaction();
            $id_solicitud = $request->input('id_solicitud');
            $usuario = strtoupper($request->input('usuario'));
            $fecha_modifica = Carbon::now()->format('Y-m-d H:i:s');
            $solicitud = new Solicitud();
            $consultar = Solicitud::
            select('id_solicitud','cantidad_pendiente')
            ->where('id_solicitud', $id_solicitud)
            ->where('estado', 'Completado')
            ->get();
            if (count($consultar) > 0) {
                return 'No se puede confirmar';
            } else {
                $items  = $request->input('detalles');
                for ($i = 0; $i <count($items) ; $i++) { 
                    $detalle = new Detalle();
                    $consultaDetalle = Detalle::
                    select('id_detalle','cantidad_solicitada','fk_solicitud','fk_articulo')
                    ->where('fk_solicitud',$id_solicitud)
                    ->where('fk_articulo', $items[$i]['fk_articulo'])
                    ->get();
                    $detalleData['cantidad_confirmada'] = $items[$i]['cantidad_solicitada'];
                    $detalleData['cantidad_pendiente']  = $consultaDetalle[0]['cantidad_solicitada'] - $detalleData['cantidad_confirmada'];
                    $detalleData['estado'] = 'Completado';
                    $detalleData['usuario_modifica'] = $usuario;
                    $detalleData['fecha_modifica'] = $fecha_modifica;
                    $detalle = Detalle::where('fk_solicitud', $id_solicitud)->update($detalleData);

                    $solicitudActualizar = new Solicitud();
                    $solicitudData['cantidad_confirmada'] = $this->sumarCantidadConfirmada($id_solicitud);
                    $solicitudData['cantidad_pendiente']  = $this->sumarCantidadPendiente($id_solicitud);
                    $solicitudData['estado'] = 'Completado';
                    $solicitudData['fecha_modifica'] = $fecha_modifica;
                    $solicitudData['usuario_modifica'] = $usuario;
                    $solicitudActualizar = Solicitud::where('id_solicitud', $id_solicitud)->update($solicitudData);

                    $articulo = New Articulo();
                    $articuloDataSolicitud['cantidad_pedida']  = $this->sumarCantidadPedidaArticulo($items[$i]['fk_articulo']) - $items[$i]['cantidad_solicitada'];
                    $articuloDataSolicitud['usuario_modifica'] = $usuario;
                    $articuloDataSolicitud['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
                    $articuloDataSolicitud['stock']  = $this->sumarCantidadStockArticulo($items[$i]['fk_articulo']) + $items[$i]['cantidad_solicitada']; 
                    $articuloDataSolicitud['estado'] = 'Disponible';
                    $articulo = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($articuloDataSolicitud);

                    
                    DB::commit();
                    return response()->json([
                        "ok" =>true,
                        "data"=>$solicitud,
                        "confirmado" =>'Se confirmó satisfactoriamente'
                    ]);
                }
            }
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data"=>$th->getMessage(),
                "errorConfirmado" =>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }
    }

    public function registrarSalida(SalidaRegistrarRequest $request){
        try {
            DB::beginTransaction();
            $usuario = strtoupper($request->input('usuario'));
            $tipo_accion = strtoupper($request->input('tipo_accion'));
            if ($tipo_accion === 'SALIDA') {
                $solicitud = new Solicitud();
                $solicitud->fk_tipo_solicitud = $request->input('fk_tipo_solicitud');
                $solicitud->fk_despacho  = $request->input('fk_despacho');
                $solicitud->fecha_salida = Utilidades::formatoFecha($request->input('fecha_salida'));
                $solicitud->incidencia   = $request->input('incidencia');
                $solicitud->usuario_crea = $usuario;
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
                }

                DB::commit();
                return response()->json([
                    "ok" =>true,
                    "data"=>$solicitud,
                    "exitoso" =>'Se guardo satisfactoriamente'
                ]);
            }
    
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data"=>$th->getMessage(),
                "error" =>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }
    }

    public function editarSalida(EditarSaalidaRequest $request){
        try {
           DB::beginTransaction();
           $id_solicitud = $request->input('id_solicitud');
           $fk_tipo_solicitud = $request->input('fk_tipo_solicitud');
           $solicitud = new Solicitud();
           $validar  = Solicitud::
           where('id_solicitud', $id_solicitud)
           ->where('estado', 'Completado')
           ->get();
           if (count($validar) > 0) {
            return 'No se puede editar esta solicitud';
           } else {
            $data['fk_despacho']    = $request->input('fk_despacho');
            $data['fecha_entrada']  = Utilidades::formatoFecha($request->input('fecha_salida'));
            $data['incidencia']     = $request->input('incidencia');
            $data['usuario_modifica'] = strtoupper($request->input('usuario'));
            $data['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
            $solicitud = Solicitud::where('id_solicitud', $id_solicitud)->update($data);

            $items = $request->input('detalles');
            for ($i=0; $i <count($items) ; $i++) { 
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
            }

            DB::commit();
            return response()->json([
             "data" =>true,
             "ok" =>$solicitud,
             "exitoso" =>'Se guardo satisfactoriamente'
            ]);
           }
        } catch (\Exception $th) {
            
        }
    }

    public function eliminarSalida(EliminarSalidaRequest $request) {
        try {
            DB::beginTransaction();
            $usuario = strtoupper($request->input('usuario'));
            $id_solicitud = $request->input('id_solicitud');
            $solicitudes = new Solicitud();
            $consultaSolicitud = Solicitud::
            select('id_solicitud','cantidad_confirmada')
            ->where('id_solicitud',$id_solicitud)
            ->where('cantidad_confirmada', '>', 0)
            ->get();
            if (count($consultaSolicitud) > 0) {
                return 'No se puede eliminar esta solicitud';
            } else {
                $items = $request->input('detalles');
                for ($i=0; $i <count($items) ; $i++) { 
                    $detalle = new Detalle();
                    $detalle = Detalle::where('id_detalle', $items[$i]['id_detalle'])->delete();
                    $solicitud = New Solicitud();
                    $solicitud = Solicitud::where('id_solicitud', $id_solicitud)->delete();
                    DB::commit();
                    return response()->json([
                     "ok" =>true,
                     "data"=>$solicitudes,
                     "eliminarSolicitud" =>'Se eliminó satisfactoriamente'
                 ]);

                }
            }
            
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                "ok" =>false,
                "data"=>$th->getMessage(),
                "errorEliminarSolicitud" =>'Hubo un error consulte con el Administrador del sistema'
            ]);
        }
    }

    public function confirmarSolicitudSalida(ConfirmarSalidaRequest $request){
        try {
           DB::beginTransaction();
           $id_solicitud = $request->input('id_solicitud');
           $usuario = strtoupper($request->input('usuario'));
           $fecha_modifica = Carbon::now()->format('Y-m-d H:i:s');
           $solicitud = new Solicitud();
           $consultar = Solicitud::
           select('id_solicitud','cantidad_pendiente')
           ->where('id_solicitud', $id_solicitud)
           ->where('estado', 'Completado')
           ->get();
           if (count($consultar) > 0) {
            return 'No se puede confirmar está solicitud';
           } else {
            $items  = $request->input('detalles');
            for ($i = 0; $i <count($items) ; $i++) { 
                $detalle = new Detalle();
                $consultaDetalle = Detalle::
                select('id_detalle','cantidad_solicitada','fk_solicitud','fk_articulo')
                ->where('fk_solicitud',$id_solicitud)
                ->where('fk_articulo', $items[$i]['fk_articulo'])
                ->get();
                $detalleData['cantidad_solicitada'] = $items[$i]['cantidad_solicitada'];
                $detalleData['cantidad_confirmada'] = $items[$i]['cantidad_solicitada'];
                $detalleData['cantidad_pendiente']  = $consultaDetalle[0]['cantidad_solicitada'] - $detalleData['cantidad_confirmada'];
                $detalleData['estado'] = 'Completado';
                $detalleData['usuario_modifica'] = $usuario;
                $detalleData['fecha_modifica']   = $fecha_modifica;
                $detalle = Detalle::where('fk_solicitud', $id_solicitud)->update($detalleData);

                $solicitudActualizar = new Solicitud();
                $solicitudData['cantidad_solicitada'] = $this->sumarCantidadSolicitada($id_solicitud);
                $solicitudData['cantidad_confirmada'] = $this->sumarCantidadConfirmada($id_solicitud);
                $solicitudData['cantidad_pendiente']  = $this->sumarCantidadPendiente($id_solicitud);
                $solicitudData['estado'] = 'Completado';
                $solicitudData['fecha_modifica'] = $fecha_modifica;
                $solicitudData['usuario_modifica'] = $usuario;
                $solicitudActualizar = Solicitud::where('id_solicitud', $id_solicitud)->update($solicitudData);

                $articulo = New Articulo();
                $consultarStockArticulo = Articulo::
                select('id_articulo','stock')
                ->where('id_articulo', $items[$i]['fk_articulo'])
                ->get();
                if (count($consultarStockArticulo) > 0) {
                    if ($consultarStockArticulo[0]['stock'] == 0) {
                        $articuloDataSolicitud['usuario_modifica'] = $usuario;
                        $articuloDataSolicitud['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
                        $articuloDataSolicitud['stock']  = $consultarStockArticulo[0]['stock'] - $items[$i]['cantidad_solicitada']; 
                        $articuloDataSolicitud['estado'] = 'Disponible';
                        $articulo = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($articuloDataSolicitud);
    
                    } else {
                        $articuloDataSolicitud['usuario_modifica'] = $usuario;
                        $articuloDataSolicitud['fecha_modifica']   = Carbon::now()->format('Y-m-d H:i:s');
                        $articuloDataSolicitud['stock']  = $consultarStockArticulo[0]['stock'] - $items[$i]['cantidad_solicitada']; 
                        $articuloDataSolicitud['estado'] = 'Agotado';
                        $articulo = Articulo::where('id_articulo', $items[$i]['fk_articulo'])->update($articuloDataSolicitud);
                    }
                    
                }

                DB::commit();
                return response()->json([
                    "ok" =>true,
                    "data"=>$solicitud,
                    "confirmado" =>'Se confirmó satisfactoriamente'
                ]);
            }
           }
        } catch (\Exception $th) {
            
        }
    }

    public function contarSolicitudesEntrada(){
        $solicitud = VistaSolicitud::
        select('id_solicitud')
        ->get();
        return response()->json([
            "ok"=>true,
            "data"=>$solicitud->count()
        ]);
    }

    public function mostrarDetalleSolicitud($id_solicitud){
        $solicitud = VistaSolicitud::
        select('id_solicitud','tipo_solicitud','fk_despacho','despacho','fecha_entrada','fecha_salida','incidencia','cantidad_solicitada','preparado_por','estado','num_solicitud','fk_tipo_solicitud')                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
        ->where('id_solicitud', $id_solicitud)
        ->where('estado', 'Pendiente')
        ->first(); 

        $detalleArticulos = VistaDetalle::
        select('id_detalle','no_item','fk_tipo_solicitud','fk_articulo','fk_solicitud','codigo','referencia','categoria','marca','modelo','color','cantidad_solicitada')
        ->where('fk_solicitud', $id_solicitud)
        ->where('estado', 'Pendiente')
        ->orderBy('id_detalle', 'asc')
        ->get();

        $solicitud->articulos = $detalleArticulos;
        return response()->json([
            "ok"=>true,
            "data"=>$solicitud
        ]);
    }

    public function mostrarArticulosDisponiblesEntrada(){
        $solicitud = VistaArticulosEntrada::all();
        return response()->json([
            "ok"=>true,
            "data"=>$solicitud
        ]);
    }

    

  
   

    


    
}
