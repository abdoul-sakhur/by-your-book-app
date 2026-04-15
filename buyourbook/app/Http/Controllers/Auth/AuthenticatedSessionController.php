<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Authentifie l'utilisateur et redirige selon son rôle.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended($this->redirectByRole());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Retourne l'URL de redirection selon le rôle de l'utilisateur connecté.
     */
    private function redirectByRole(): string
    {
        return match (Auth::user()->role) {
            UserRole::Admin => route('admin.dashboard', absolute: false),
            UserRole::Seller => route('seller.dashboard', absolute: false),
            default => route('home', absolute: false),
        };
    }
}
