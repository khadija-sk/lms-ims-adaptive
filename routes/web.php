<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return redirect('/frontend/auth-login-drag.html');
})->name('login');

Route::get('/', function () {
    return redirect('/frontend/auth-login-drag.html');
});