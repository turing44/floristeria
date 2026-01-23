<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\MensajeController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get("/entregas", [EntregaController::class, "index"]);
Route::post("/entregas", [EntregaController::class, "store"]);
Route::get("/entregas/{entrega}", [EntregaController::class, "show"]);
Route::put("/entregas/{entrega}", [EntregaController::class, "update"]);
Route::delete("/entregas/{entrega}", [EntregaController::class, "destroy"]);

Route::get("/entregas/archivadas", [EntregaController::class, "obtenerEliminadas"]);
Route::get("/entregas/archivadas/{id}", [EntregaController::class, "obtenerEntregaEliminada"]);

Route::get('/entregas/pdf/{id}', [EntregaController::class, 'generarPdf']);




Route::get("/reservas", [ReservaController::class, "index"]);
Route::post("/reservas", [ReservaController::class, "store"]);
Route::get("/reservas/{reserva}", [ReservaController::class, "show"]); 
Route::put("/reservas/{reserva}", [ReservaController::class, "update"]);
Route::delete("/reservas/{reserva}", [ReservaController::class, "destroy"]);

Route::get("/reservas/archivadas", [ReservaController::class, "obtenerEliminadas"]);
Route::get("/reservas/archivadas/{id}", [ReservaController::class, "obtenerReservaEliminada"]);
Route::get('/reservas/pdf/{id}', [ReservaController::class, 'generarPdf']);

Route::post('/mensaje/pdf', [MensajeController::class, 'generarPdf']);