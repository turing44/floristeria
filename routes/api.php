<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/pedidos', [PedidoController::class, 'index']);
Route::post('/pedidos', [PedidoController::class, 'store']);
Route::get('/pedidos/{pedido}', [PedidoController::class, 'show']);
Route::put('/pedidos/{pedido}', [PedidoController::class, 'update']);

Route::middleware(["auth:sanctum", "rol:admin,experto"])->group(function (){
    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy']);
});


Route::middleware("auth:sanctum")->group( function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

