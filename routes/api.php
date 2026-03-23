<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContratoPedidoController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\GoogleImportController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ReservaController;

Route::prefix('contratos')->group(function () {
    Route::get('/entregas', [ContratoPedidoController::class, 'entrega']);
    Route::get('/reservas', [ContratoPedidoController::class, 'reserva']);
});

Route::prefix('entregas')->group(function () {
    Route::get('/', [EntregaController::class, 'index']);
    Route::post('/', [EntregaController::class, 'store']);
    Route::get('/archivadas', [EntregaController::class, 'obtenerEliminadas']);
    Route::get('/archivadas/{id}', [EntregaController::class, 'obtenerEntregaEliminada']);
    Route::get('/pdf/{id}', [EntregaController::class, 'generarPdf']);
    Route::post('/restaurar/{id}', [EntregaController::class, 'restaurar']);
    Route::get('/{entrega}', [EntregaController::class, 'show']);
    Route::put('/{entrega}', [EntregaController::class, 'update']);
    Route::delete('/{entrega}', [EntregaController::class, 'destroy']);
});

Route::prefix('reservas')->group(function () {
    Route::get('/', [ReservaController::class, 'index']);
    Route::post('/', [ReservaController::class, 'store']);
    Route::get('/archivadas', [ReservaController::class, 'obtenerEliminadas']);
    Route::get('/archivadas/{id}', [ReservaController::class, 'obtenerReservaEliminada']);
    Route::get('/pdf/{id}', [ReservaController::class, 'generarPdf']);
    Route::post('/restaurar/{id}', [ReservaController::class, 'restaurar']);
    Route::get('/{reserva}', [ReservaController::class, 'show']);
    Route::put('/{reserva}', [ReservaController::class, 'update']);
    Route::delete('/{reserva}', [ReservaController::class, 'destroy']);
});

Route::post('/mensaje/pdf', [MensajeController::class, 'generarPdf']);

if (config('floristeria.google_habilitado')) {
    Route::get('/generar-link', [GoogleImportController::class, 'generarLink']);
    Route::get('/importar-pedidos', [GoogleImportController::class, 'importarPedidos']);
}
