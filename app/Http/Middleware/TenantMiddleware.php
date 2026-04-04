<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Institution;
use Illuminate\Support\Facades\View;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        // Busca a instituição pelo domínio cadastrado
        // Se for o domínio principal (ex: localhost), não retornará nada
        $tenant = Institution::where('domain', $host)->first();

        // Compartilha a variável $tenant com TODAS as views do Blade
        View::share('tenant', $tenant);

        return $next($request);
    }
}