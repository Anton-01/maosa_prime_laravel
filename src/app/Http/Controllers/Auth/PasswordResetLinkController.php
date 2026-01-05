<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->is_approved !== 1) {
            return back()->with('status', [
                'main_message' => '¡Proceso no válido!',
                'description' => 'El servicio de restablecimiento de contraseña no está habilitado para cuentas no validadas.',
                'alert_type' => 'warning'
            ])->withInput($request->only('email'));
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT ?
                back()->with('status', [
                    'main_message' => '¡Proceso exitoso!',
                    'description' => __($status),
                    'alert_type' => 'success'
                ])->withInput($request->only('email'))
                : back()->with('status', [
                    'main_message' => '¡Error al enviar el correo!',
                    'description' => __($status),
                    'alert_type' => 'danger'
                ])->withInput($request->only('email'));
    }
}
