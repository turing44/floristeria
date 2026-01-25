<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PedidoController;
Route::get('/', function () {
    return view('welcome');
});

// 1. Pantalla del Florista (Simulacro)
Route::get('/demo/florista', function () {
    return view('demo-florista');
});

// 2. Pantalla del Cliente (La que carga el enlace mágico)
Route::get('/pedido-magic-view/{token}', function ($token) {
    return view('demo-cliente', ['token' => $token]);
});
