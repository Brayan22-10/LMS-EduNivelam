<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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

        if (!Auth::attempt($credentials)) {
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

        if (Auth::user()->email === 'admin@environmental.com') {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'required', 
                'string', 
                'min:8', 
                'confirmed',
                function ($attribute, $value, $fail) use ($request) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('La contraseña debe contener al menos una mayúscula.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('La contraseña debe contener al menos un número.');
                    }
                    if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                        $fail('La contraseña debe contener al menos un símbolo.');
                    }
                    if (!empty($request->name) && stripos($value, $request->name) !== false) {
                        $fail('La contraseña no puede contener el nombre de la cuenta.');
                    }
                }
            ],
        ], [
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $email = Str::lower($request->email);

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            return back()->withInput($request->except('password', 'password_confirmation'))
                       ->with('registration_error', 'Se produjo un error. Por favor, vuelva a intentarlo.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
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
}