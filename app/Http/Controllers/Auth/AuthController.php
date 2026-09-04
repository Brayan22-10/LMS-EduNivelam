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

    return redirect()->intended(route('dashboard'));
    }
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}