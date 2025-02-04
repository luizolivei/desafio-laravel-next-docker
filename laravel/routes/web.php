<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['versao' => app()->version()];
});

// Rota para testar o Xdebug
Route::get('/test-xdebug', function () {
    $a = 5;
    $b = 10;
    $sum = $a + $b;

    for ($i = 0; $i < 3; $i++) {
        //colocar um breakpoint aqui
        $sum += $i;
    }

    $mensagem = "O resultado final é: " . $sum;

    // Retorna os dados como JSON
    return response()->json([
        'a'         => $a,
        'b'         => $b,
        'sum'       => $sum,
        'mensagem'  => $mensagem,
    ]);
});

require __DIR__.'/auth.php';
