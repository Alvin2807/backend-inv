<?php

use App\Http\Controllers\Api\ArticulosController;
use App\Http\Controllers\Api\CategoriasController;
use App\Http\Controllers\Api\ColoresController;
use App\Http\Controllers\Api\DespachosController;
use App\Http\Controllers\Api\MarcasController;
use App\Http\Controllers\Api\ModelosController;
use App\Http\Controllers\Api\NomenclaturasController;
use App\Http\Controllers\Api\SolicitudesController;
use App\Http\Controllers\Api\TipoSolicitudController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TipoEntradasController;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('registrar_salida', [SolicitudesController::class, 'registrarSalida']);
Route::post('registrar_usuario', [UserController::class,'registrarUsuario']);
Route::put('editar_articulo', [ArticulosController::class,'editarArticulo']);
Route::put('eliminar_solicitud_salida', [SolicitudesController::class,'eliminarSalida']);
Route::put('confirmar_salida', [SolicitudesController::class,'confirmarSolicitudSalida']);
Route::put('editar_salida',[SolicitudesController::class,'editarSalida']);
Route::put('eliminar_articulo_solicitud', [SolicitudesController::class,'eliminarArticuloSolicitud']);
Route::put('confirmar_solicitud', [SolicitudesController::class,'confirmarSolicitud']);
Route::put('eliminar_solicitud', [SolicitudesController::class,'eliminarSolicitud']);
Route::post('iniciar_sesion',[UserController::class,'iniciar_sesion']);
Route::post('verificar_existe_num_solicitud',[SolicitudesController::class,'mostrarNumeroSolicitudExisteRegistro']);
Route::put('editar_solicitud',[SolicitudesController::class,'editarSolicitud']);
Route::get('mostrar_perfil_usuarios', [UserController::class,'mostrarPerfil']);
Route::get('select_modelo_marca/{id_marca}', [ModelosController::class,'mostrarModeloMarca']);
Route::get('despachos_por_entrada', [DespachosController::class,'mostrarDespachosporEntrada']);
Route::get('contar_solicitud_entrada', [SolicitudesController::class,'contarSolicitudesEntrada']);
Route::get('detalle_de_solicitud/{id_solicitud}', [SolicitudesController::class,'mostrarDetalleSolicitud']);
Route::get('articulos_disponibles_entradas', [SolicitudesController::class,'mostrarArticulosDisponiblesEntrada']);
Route::get('despacho_almacen', [DespachosController::class,'DespachoAlmacen']);
Route::get('tipo_entrada_almacen', [TipoEntradasController::class,'entradaPorAlmacen']);
Route::get('elegir_tipo_entrada/{id_despacho}', [TipoEntradasController::class,'elegirTipoEntrada']);
Route::get('articulos_entradas',[SolicitudesController::class,'vistaArticulosEntradas']);
Route::get('detalle_de_entrada_articulo/{id_articulo}', [SolicitudesController::class,'vistaArticulosDetalleEntradas']);
Route::get('meses_entrada/{id_articulo}',[SolicitudesController::class,'vistaMesesEntada']);
Route::get('meses_articulos_entradas/{id_articulo}',[SolicitudesController::class,'VistaMesEntradaXArticulos']);
Route::get('detalle_entrada_de_articulo_x_mes_x_fecha/{id_articulo}',[SolicitudesController::class,'DetalleEntradaxArticuloxMes']);
Route::apiResource('solicitudes', SolicitudesController::class);
Route::apiResource('tipo_solicitudes', TipoSolicitudController::class);
Route::apiResource('colores', ColoresController::class);
Route::apiResource('marcas', MarcasController::class);
Route::apiResource('nomenclaturas', NomenclaturasController::class);
Route::apiResource('articulos', ArticulosController::class);
Route::apiResource('categorias', CategoriasController::class);
Route::apiResource('tipo_entradas', TipoEntradasController::class);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
