<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

collect(['/', '/login', '/register'])->each(function ($uri) {
    Route::get($uri, static fn () => redirect()->route('filament.admin.auth.login'));
});
