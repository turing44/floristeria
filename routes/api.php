<?php

use App\Http\Controllers\EntregaController;
use App\Http\Controllers\ReservaController;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/entregas", [EntregaController::class, "index"]);
Route::get("/entregas/archivadas", [EntregaController::class, "obtenerEliminadas"]);
Route::post("/entregas", [EntregaController::class, "store"]);
Route::get("/entregas/{entrega}", [EntregaController::class, "show"]);
Route::delete("/entregas/{entrega}", [EntregaController::class, "destroy"]);


Route::get("/reservas", [ReservaController::class, "index"]);
Route::get("/reservas/archivadas", [ReservaController::class, "obtenerEliminadas"]);
Route::post("/reservas", [ReservaController::class, "store"]);
Route::get("/reservas/{reserva}", [ReservaController::class, "show"]);
Route::delete("/reservas/{reserva}", [ReservaController::class, "destroy"]);

Route::get("/entrega/{entrega}/pdf");
Route::get("/reserva/{reserva}/pdf");