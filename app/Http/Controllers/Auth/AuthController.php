<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = Str::lower($request->input('email'));
        $throttleKey = $email . '|' . $request->ip();
        $lockoutCacheKey = 'lockout_count_' . $throttleKey;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withInput();
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->has('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, 300); 

            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $blockCount = cache()->get($lockoutCacheKey, 0) + 1;
                cache()->put($lockoutCacheKey, $blockCount, now()->addHours(24)); 

                $customLockoutTime = 300 * pow(2, $blockCount - 1);

                RateLimiter::clear($throttleKey);
                for ($i = 0; $i < 5; $i++) {
                    RateLimiter::hit($throttleKey, $customLockoutTime);
                }
            }

            return back()->withErrors([
                'email' => 'Las credenciales no son válidas.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        cache()->forget($lockoutCacheKey);

        $request->session()->regenerate();

        // --- GESTIÓN DE CREDENCIALES MEDIANTE COOKIES SEGÚN CHECKBOX ---
        if ($remember) {
            // Guarda las credenciales en cookies cifradas por 30 días (43200 min)
            Cookie::queue('saved_email', $request->email, 43200);
            Cookie::queue('saved_password', $request->password, 43200);
            Cookie::queue('remember_checked', 'true', 43200);
        } else {
            // Borra las cookies si no marcó la opción
            Cookie::queue(Cookie::forget('saved_email'));
            Cookie::queue(Cookie::forget('saved_password'));
            Cookie::queue(Cookie::forget('remember_checked'));
        }
        // -------------------------------------------------------------

        if (Auth::user()->email === 'admin@environmental.com') {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name'),
            'email' => Str::lower($request->string('email')),
            'password' => Hash::make($request->string('password')),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = rand(100000, 999999); 
        
        $user = User::where('email', $request->email)->first();
        $user->reset_code = $code;
        $user->reset_code_expires_at = Carbon::now()->addMinutes(15); 
        $user->save();

        Mail::raw("Tu código de recuperación para EduNivelam es: {$code}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Código de recuperación de contraseña - EduNivelam');
        });

        session(['reset_email' => $user->email]);
        return redirect()->route('password.verify.form')->with('success', 'Se ha enviado un código de 6 dígitos a tu correo.');
    }

    public function showVerifyForm()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|numeric|digits:6']);

        $user = User::where('email', session('reset_email'))
                    ->where('reset_code', $request->code)
                    ->where('reset_code_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return back()->withErrors(['code' => 'El código es inválido o ha expirado.']);
        }

        session(['code_verified' => true]);
        return redirect()->route('password.reset.form');
    }

    public function showResetForm()
    {
        if (!session('code_verified')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::where('email', session('reset_email'))->first();
        
        if (!$user) {
            return redirect()->route('password.request');
        }

        $user->password = Hash::make($request->password);
        $user->reset_code = null;
        $user->reset_code_expires_at = null;
        $user->save();

        session()->forget(['reset_email', 'code_verified']);

        return redirect()->route('login')->with('success', '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.');
    }
}