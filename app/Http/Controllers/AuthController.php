<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Login;
use App\Models\Personal;

class AuthController extends Controller
{
    // Mostrar la página de inicio de sesión
    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $this->ensureIsNotRateLimited($request); //limitar los intentos antes de validar

        // Validar los datos del formulario
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key= Str::lower($request->input('username')) . '|' . $request->ip(); //para contar los intentos

        // Intentar autenticar al usuario (nombre)
        $login = Login::where('nombre', $request->username)->first();

        if ($login && Hash::check($request->password, $login->contrasena)) {//verifica la contraseña

            $personal = $login->personal;

            if ($personal && $personal->estado == 1 && $personal->tipoPersonal) {

                Auth::login($personal);
                $request->session()->regenerate(); // Evitar session fixation

                RateLimiter::clear($key); // ✅ Éxito: limpiar el contador

                // Redirigir según el tipo de personal
                switch ($personal->tipoPersonal->descripcion_per ?? null) {
                    case 'Administrador':
                        return redirect()->route('admin.index');
                    case 'Coordinador Odontologia':
                        return redirect()->route('coordinator.inicio');
                    case 'Encargado':
                        return redirect()->route('encargado.inicio');
                    default:
                        return redirect()->back()->withErrors(['login' => 'Tipo de personal no reconocido.']);
                }
            }

            return redirect()->back()->withErrors(['login' => 'El usuario ha sido inhabilitado o es inválido.']);

        }

        RateLimiter::hit($key); // bloque por intentos fallidos sin por 5 minutos

        return redirect()->back()->with(['login' => 'Credenciales incorrectas.']);

    }

protected function ensureIsNotRateLimited(Request $request)
{
    $key = Str::lower($request->input('username')) . '|' . $request->ip();

    if (! RateLimiter::tooManyAttempts($key, 3)) {
        return;
    }

    return back()->withErrors([
        'login' => 'Demasiados intentos. Por favor espera e intenta nuevamente más tarde.'
    ]);
}

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
