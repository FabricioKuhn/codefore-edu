<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // 1. Super Admin vai pro painel CodeForce
        // Note que estou checando a role agora, já que você tem isso no banco!
        if ($user->role === 'super_admin' || $user->is_super_admin) {
            return redirect()->route('superadmin.dashboard');
        }

        // 2. Admin do Tenant vai para /admin
        if ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        // 3. Professor vai para /professor
        if ($user->role === 'teacher') {
            return redirect()->intended('/professor/dashboard');
        }

        // 4. Aluno vai para /aluno
        if ($user->role === 'student') {
            return redirect()->intended('/aluno/dashboard');
        }

        // Fallback de segurança
        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
