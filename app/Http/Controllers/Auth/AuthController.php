<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

    public function register(RegisterUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name'),
            'email' => Str::lower($request->string('email')),
            'password' => Hash::make($request->string('password')),
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