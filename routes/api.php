<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\PedidoController;   
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\ReservaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/pedidos', [PedidoController::class, 'store']);

Route::get('/pedidos', [PedidoController::class, 'index']);

Route::get('/pedidos/{pedido}/pdf', [PedidoController::class, 'generarpdf']);


// ========================================================================
// 2. ÁREA DE LOGÍSTICA DE ENTREGAS 
// ========================================================================

Route::get("/entregas", [EntregaController::class, "index"]);
Route::post("/entregas", [EntregaController::class, "store"]);
Route::get("/entregas/{entrega}", [EntregaController::class, "show"]);
Route::put("/entregas/{entrega}", [EntregaController::class, "update"]);
Route::delete("/entregas/{entrega}", [EntregaController::class, "destroy"]);

// Rutas de Archivo (Soft Deletes)
Route::get("/entregas/archivadas", [EntregaController::class, "obtenerEliminadas"]);
Route::get("/entregas/{id}/archivadas", [EntregaController::class, "obtenerEntregaEliminada"]);


// ========================================================================
// 3. ÁREA DE LOGÍSTICA DE RESERVAS 
// ========================================================================

Route::get("/reservas", [ReservaController::class, "index"]);
Route::post("/reservas", [ReservaController::class, "store"]);
// Route::get("/reservas/{reserva}", [ReservaController::class, "show"]);
Route::put("/reservas/{reserva}", [ReservaController::class, "update"]);
Route::delete("/reservas/{reserva}", [ReservaController::class, "destroy"]);

// Rutas de Archivo (Soft Deletes)
Route::get("/reservas/archivadas", [ReservaController::class, "obtenerEliminadas"]);
Route::get("/reservas/{id}/archivadas", [ReservaController::class, "obtenerReservaEliminada"]);