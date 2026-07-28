<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.events.index');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.events.index'));
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    /** Cambio de contraseña del propio administrador desde el panel. */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Ingresa tu contraseña actual.',
            'password.required'         => 'Ingresa la nueva contraseña.',
            'password.confirmed'        => 'La nueva contraseña y su confirmación no coinciden.',
            'password.min'              => 'La nueva contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        // Mantener la sesión activa con la nueva credencial.
        $request->session()->regenerate();

        return back()->with('ok', 'Tu contraseña se actualizó correctamente.');
    }
}
