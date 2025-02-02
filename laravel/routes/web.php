<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['version' => app()->version()];
});

// Rota para testar o Xdebug
Route::get('/test-xdebug', function () {
    // Inicialização de variáveis para teste
    $a = 5;
    $b = 10;
    $sum = $a + $b;

    // Coloque um breakpoint na linha abaixo para inspecionar as variáveis
    for ($i = 0; $i < 3; $i++) {
        $sum += $i;
    }

    // Outra variável para exemplificar
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
