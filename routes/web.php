<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/volt-demo', function () {
    return view('volt-demo');
});
