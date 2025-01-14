<?php

use App\Http\Controllers\Api\ArticulosController;
use App\Http\Controllers\Api\NomenclaturasController;
use App\Http\Controllers\Api\SolicitudesController;
use App\Http\Controllers\Api\UserController;
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
Route::post('registrar_usuario', [UserController::class,'registrarUsuario']);
Route::put('editar_articulo', [ArticulosController::class,'editarArticulo']);
Route::post('iniciar_sesion',[UserController::class,'iniciar_sesion']);
Route::apiResource('solicitudes', SolicitudesController::class);
Route::apiResource('nomenclaturas', NomenclaturasController::class);
Route::apiResource('articulos', ArticulosController::class);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
