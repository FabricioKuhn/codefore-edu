<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Se o usuário não estiver logado, ou se a coluna is_super_admin for false/nula...
        if (!auth()->check() || !auth()->user()->is_super_admin) {
            // Expulsa ele da página com um Erro 403 (Acesso Negado)
            abort(403, 'Acesso restrito apenas para a equipe CodeForce.');
        }

        // Se ele for super admin, deixa passar
        return $next($request);
    }
}
