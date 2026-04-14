<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $landingPath = resource_path('views/welcome.blade.php');

    if (is_file($landingPath)) {
        return response()->file($landingPath, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    return response('<h1>Fleetly backend</h1><p>Landing no disponible.</p>', 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
});
