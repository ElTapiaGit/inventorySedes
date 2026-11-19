<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Login;
use App\Models\Personal;

class AuthController extends Controller
{
    // Mostrar la pagina de inicio de sesion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $key = Str::lower($request->input('username')) . '|' . $request->ip();

        // Control de tasa de intentos fallidos (Rate Limiter: maximo 3 intentos)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            $mensajeBloqueo = "Demasiados intentos de acceso. Por favor espere {$minutes} minuto(s) antes de intentar nuevamente.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $mensajeBloqueo,
                    'retry_after' => $seconds,
                ], 429);
            }

            return redirect()->back()->withErrors(['login' => $mensajeBloqueo]);
        }

        // Validar campos de entrada
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Buscar registro eliminando espacios accidentales, pero respetando mayúsculas/minúsculas (utf8_bin)
        $usernameLimpio = trim((string) $request->input('username'));
        $login = Login::where('nombre', $usernameLimpio)->first();

        // Validar existencia de usuario y coincidencia de clave con hash
        if ($login && Hash::check((string) $request->input('password'), $login->contrasena)) {
            $personal = $login->personal;

            // Verificar estado activo del personal y definicion de rol
            if ($personal && $personal->estado == 1 && $personal->tipoPersonal) {
                Auth::login($personal);
                $request->session()->regenerate();

                // Limpiar contador de intentos ante autenticacion exitosa
                RateLimiter::clear($key);

                // Determinar ruta segun el rol del usuario
                $rolDescripcion = $personal->tipoPersonal->descripcion_per ?? null;
                $redirectUrl = null;

                if ($personal->esCoordinador()) {
                    $redirectUrl = route('coordinator.inicio');
                } elseif ($rolDescripcion === 'Administrador') {
                    $redirectUrl = route('admin.index');
                } elseif ($rolDescripcion === 'Encargado') {
                    $redirectUrl = route('encargado.inicio');
                }

                if (!$redirectUrl) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Tipo de personal no reconocido o sin permisos asignados.',
                        ], 403);
                    }
                    return redirect()->back()->withErrors(['login' => 'Tipo de personal no reconocido o sin permisos asignados.']);
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Autenticacion exitosa. Redirigiendo...',
                        'redirect' => $redirectUrl,
                    ]);
                }

                return redirect()->intended($redirectUrl);
            }

            $mensajeInhabilitado = 'El usuario ha sido inhabilitado o no cuenta con acceso activo al sistema.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $mensajeInhabilitado,
                ], 403);
            }

            return redirect()->back()->withErrors(['login' => $mensajeInhabilitado]);
        }

        // Registrar intento fallido (bloqueo durante 300 segundos tras alcanzar el limite)
        RateLimiter::hit($key, 300);

        // Validar si el intento recien registrado alcanzo el limite de bloqueos
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            $mensajeBloqueo = "Ha superado el limite de intentos permitidos. Por favor espere {$minutes} minuto(s) antes de intentar nuevamente.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $mensajeBloqueo,
                    'retry_after' => $seconds,
                ], 429);
            }

            return redirect()->back()->withErrors(['login' => $mensajeBloqueo]);
        }

        $intentosRestantes = RateLimiter::remaining($key, 3);
        $mensajeError = 'Credenciales incorrectas.';
        if ($intentosRestantes > 0) {
            $mensajeError .= " Intentos restantes: {$intentosRestantes}.";
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $mensajeError,
            ], 401);
        }

        return redirect()->back()->withErrors(['login' => $mensajeError]);
    }

    // Cerrar sesion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
