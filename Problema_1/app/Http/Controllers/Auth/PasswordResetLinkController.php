<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\Empleado; // o User, según tu modelo real
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Mostrar formulario de recuperación
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar enlace de recuperación SOLO a admins
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Buscar usuario
        $user = Empleado::where('email', $request->email)->first();

        // Email no existe o no es admin
        if (!$user || $user->tipo !== 'admin') {
            return back()->withErrors([
                'email' => 'Solo los administradores pueden recuperar la contraseña.',
            ]);
        }

        // Enviar enlace
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
