<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::warning('Google OAuth falló: ' . $e->getMessage());

            return response()->view('errors.google-unregistered', [
                'message' => 'No pudimos completar el inicio de sesión con Google. Inténtalo de nuevo.'
            ], 400);
        }

        $email     = Str::lower((string) $google->getEmail());
        $googleId  = (string) $google->getId();
        $verified  = (bool) ($google->user['email_verified'] ?? false);

        if ($email === '' || ! $verified) {
            return response()->view('errors.google-unregistered', [
                'message' => 'Tu cuenta de Google no tiene un correo verificado.'
            ], 400);
        }

        $user = User::where('google_id', $googleId)->first()
            ?? User::where('email', $email)->first();

        if ($user) {
            if ($user->google_id && (string) $user->google_id !== $googleId) {
                return response()->view('errors.google-unregistered', [
                    'message' => 'Este correo ya está vinculado a otra cuenta de Google.'
                ], 400);
            }

            if ($user->role === 'teacher' && ! $user->is_approved) {
                return response()->view('errors.google-unregistered', [
                    'message' => 'Tu cuenta de docente se encuentra pendiente de aprobación por el administrador.'
                ], 403);
            }

            if ($user->is_active === false) {
                $this->securityLog(
                    $request,
                    $user->id,
                    $email,
                    'failed',
                    'inactive_account_access',
                    "Intento de acceso bloqueado: Cuenta desactivada para {$email}"
                );

                return response()->view('errors.google-unregistered', [
                    'message' => 'Tu cuenta ha sido desactivada por un administrador.'
                ], 403);
            }

            if (! $user->google_id) {
                $user->google_id = $googleId;
            }
            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
            }
            if ($user->isDirty()) {
                $user->save();
            }
        } else {
            return response()->view('errors.google-unregistered', [
                'message' => 'El correo electrónico no está registrado en EduNivelam.'
            ], 404);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $this->securityLog(
            $request,
            $user->id,
            $user->email,
            'success',
            'successful_login',
            "Inicio de sesión exitoso con Google para el usuario ID: {$user->id}"
        );

        return redirect()->intended(route('home'));
    }

    private function securityLog(Request $request, ?int $userId, string $email, string $status, string $event, string $description): void
    {
        DB::table('security_logs')->insert([
            'user_id'     => $userId,
            'email'       => $email,
            'ip_address'  => $request->ip(),
            'status'      => $status,
            'event_type'  => $event,
            'description' => $description,
            'user_agent'  => $request->header('User-Agent'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function fail(string $message)
    {
        return redirect()->route('login')->with('error', $message);
    }
}