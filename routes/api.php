<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PedidoController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/pedidos", [PedidoController::class, "index"]);
Route::post('/pedidos', [PedidoController::class, "store"]);
Route::delete('/pedidos/{id}', [PedidoController::class, "destroy"]);
Route::get('/pedidos/{id}', [PedidoController::class, "show"]);

Route::get('/pedidos/cliente/{cliente}', [PedidoController::class, "pedidoByCliente"]);
Route::get('/pedidos/fecha/{fecha}', [PedidoController::class, "pedidoByFechaEntrega"]);
Route::get('/pedidos/direccion/{direccion}', [PedidoController::class, "pedidoByDireccion"]);


