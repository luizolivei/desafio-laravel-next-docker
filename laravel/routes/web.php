<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['version' => app()->version()];
});

require __DIR__.'/auth.php';
