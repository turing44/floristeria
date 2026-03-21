<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\GoogleImportController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (config('floristeria.google_habilitado')) {
    Schedule::call(function () {
        app(GoogleImportController::class)->importarPedidos();
        Log::info('Importacion automatica de Google ejecutada.');
    })->everyTwoMinutes();
}
