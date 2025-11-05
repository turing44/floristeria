<?php

use App\Http\Controllers\EntregaController;
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

