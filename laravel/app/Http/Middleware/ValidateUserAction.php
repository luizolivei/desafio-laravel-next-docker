<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateUserAction
{
    /**
     * Manipula uma solicitação de entrada.
     */
    public function handle(Request $request, Closure $next)
    {
        // Pega o ID do usuário passado pela URL
        $userIdInRoute = $request->route('user_id');

        // Verifica se o usuário autenticado é o mesmo da rota
        if (Auth::check() && Auth::id() === (int)$userIdInRoute) {
            return $next($request);  // Permite o acesso se for o mesmo usuário
        }

        return response()->json([
            'error' => 'Ação não permitida. Você só pode gerenciar suas próprias músicas.'
        ], 403);
    }
}
