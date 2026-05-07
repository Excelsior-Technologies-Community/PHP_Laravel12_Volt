<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/volt-demo');
});

Route::get('/volt-demo', function () {
    return view('volt-demo');
});