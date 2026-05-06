<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/caracteristicas', function () {
    return view('caracteristicas');
});

Route::get('/planes', function () {
    return view('planes');
});

Route::get('/faq/{category}', function ($category) {
    $validCategories = ['car-sharing', 'reservas', 'carga', 'pagos', 'ayuda', 'empresas'];
    if (!in_array($category, $validCategories)) {
        abort(404);
    }
    return view("faq.$category");
});
