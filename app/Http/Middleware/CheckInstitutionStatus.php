<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstitutionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // 1. Verifica se o usuário está logado
    // 2. Ignora a verificação se for Super Admin (ele precisa acessar o painel para desbloquear)
    // 3. Verifica se o usuário pertence a uma instituição
    if (auth()->check() && auth()->user()->role !== 'superadmin' && auth()->user()->institution_id) {
        
        $institution = auth()->user()->institution;

        // Se a instituição estiver inativa (status = false/0)
        if (!$institution || !$institution->status) {
            auth()->logout(); // Desloga o usuário imediatamente

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'O acesso da sua instituição foi suspenso. Entre em contato com o administrador.'
            ]);
        }
    }

    return $next($request);
}
}
