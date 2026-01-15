<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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


// ========================================================================
// 1. ÁREA DE LOGÍSTICA DE ENTREGAS 🚚
// ========================================================================
Route::get("/entregas/archivadas", [EntregaController::class, "obtenerEliminadas"]);
Route::get("/entregas/{id}/archivadas", [EntregaController::class, "obtenerEntregaEliminada"]);

// El nuevo endpoint para el PDF
Route::get('/entregas/{entrega}/pdf', [EntregaController::class, 'generarPdf']);

// CRUD Principal
Route::get("/entregas", [EntregaController::class, "index"]);
Route::post("/entregas", [EntregaController::class, "store"]);
Route::get("/entregas/{entrega}", [EntregaController::class, "show"]);
Route::put("/entregas/{entrega}", [EntregaController::class, "update"]);
Route::delete("/entregas/{entrega}", [EntregaController::class, "destroy"]);


// ========================================================================
// 3. ÁREA DE LOGÍSTICA DE RESERVAS 🛍️
// ========================================================================

// Rutas Específicas
Route::get("/reservas/archivadas", [ReservaController::class, "obtenerEliminadas"]);
Route::get("/reservas/{id}/archivadas", [ReservaController::class, "obtenerReservaEliminada"]);

// El nuevo endpoint para el PDF
Route::get('/reservas/{reserva}/pdf', [ReservaController::class, 'generarPdf']);
// CRUD Principal
Route::get("/reservas", [ReservaController::class, "index"]);
Route::post("/reservas", [ReservaController::class, "store"]);
Route::get("/reservas/{reserva}", [ReservaController::class, "show"]); // Lo tenías comentado
Route::put("/reservas/{reserva}", [ReservaController::class, "update"]);
Route::delete("/reservas/{reserva}", [ReservaController::class, "destroy"]);