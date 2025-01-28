<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/login', static function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/register', static function () {
    return redirect()->route('filament.admin.auth.login');
});
