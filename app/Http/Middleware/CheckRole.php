<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * $roles pode receber várias roles separadas por vírgula (ex: 'admin', 'teacher')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verifica se o usuário está logado
        if (! $request->user()) {
            return redirect('login');
        }

        // Se a role do usuário estiver na lista de permitidas, deixa passar
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Se não tiver permissão, aborta com erro 403 (Proibido)
        abort(403, 'Acesso não autorizado para o seu perfil.');
    }
}