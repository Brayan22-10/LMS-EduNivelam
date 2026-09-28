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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered; 

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
            'password' => ['required', 'string', 'regex:/^\S*$/'],
        ], [
            'password.regex' => 'La contraseña no puede contener espacios en blanco.',
        ]);

        $user = User::where('email', $email)->first();

        if ($user && $user->role === 'teacher' && !$user->is_approved) {
            return back()->withErrors([
                'email' => 'Tu cuenta de docente se encuentra pendiente de aprobación por el administrador.',
            ])->onlyInput('email');
        }

        if ($user && ($user->is_active === false || $user->is_active === 0)) {
            DB::table('security_logs')->insert([
                'user_id' => $user->id,
                'email' => $email,
                'ip_address' => $request->ip(),
                'status' => 'failed',
                'event_type' => 'inactive_account_access',
                'description' => "Intento de acceso bloqueado: Cuenta desactivada para {$email}",
                'user_agent' => $request->header('User-Agent'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->withErrors([
                'email' => 'Tu cuenta ha sido desactivada por un administrador. No puedes iniciar sesión.',
            ])->onlyInput('email');
        }

        $remember = $request->has('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, 300); 

            DB::table('security_logs')->insert([
                'user_id' => $user ? $user->id : null,
                'email' => $email,
                'ip_address' => $request->ip(),
                'status' => 'failed',
                'event_type' => 'failed_login',
                'description' => "Intento de acceso fallido para el correo: {$email} desde la IP: {$request->ip()}",
                'user_agent' => $request->header('User-Agent'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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

        DB::table('security_logs')->insert([
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'ip_address' => $request->ip(),
            'status' => 'success',
            'event_type' => 'successful_login',
            'description' => "Inicio de sesión exitoso para el usuario ID: " . Auth::id(),
            'user_agent' => $request->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($remember) {
            Cookie::queue('saved_email', $request->email, 43200);
            Cookie::queue('saved_password', $request->password, 43200);
            Cookie::queue('remember_checked', 'true', 43200);
        } else {
            Cookie::queue(Cookie::forget('saved_email'));
            Cookie::queue(Cookie::forget('saved_password'));
            Cookie::queue(Cookie::forget('remember_checked'));
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
            'role' => 'student',
            'is_approved' => true,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('verification.notice');
    }

    public function showTeacherRegister()
    {
        return view('auth.register-teacher');
    }

    public function registerTeacher(Request $request)
    {
        $eighteenYearsAgo = Carbon::now()->subYears(18)->format('Y-m-d');

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:users,name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'dni' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:30', 'unique:users,dni'],
            'country_code' => ['required', 'string'],
            'phone' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:20', 'unique:users,phone'],
            'birthdate' => ['required', 'date', 'before_or_equal:' . $eighteenYearsAgo],
            'teaching_type' => ['required', 'in:courses,advisories'],
        ], [
            'name.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'dni.required' => 'La cédula o documento es obligatorio.',
            'dni.regex' => 'La identificación solo debe contener números (sin letras ni símbolos).',
            'dni.unique' => 'Este número de documento ya se encuentra registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono solo debe contener números (sin espacios, letras ni símbolos).',
            'phone.unique' => 'Este número de teléfono ya está en uso.',
            'birthdate.required' => 'La fecha de nacimiento es obligatoria.',
            'birthdate.before_or_equal' => 'Debes ser mayor de 18 años para registrarte como docente.',
            'teaching_type.required' => 'Selecciona el tipo de postulación.',
            'teaching_type.in' => 'Selecciona un tipo de postulación válido.',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => Str::lower($request->input('email')),
            'dni' => $request->input('dni'),
            'country_code' => $request->input('country_code'),
            'phone' => $request->input('phone'),
            'birthdate' => $request->input('birthdate'),
            'teaching_type' => $request->input('teaching_type'),
            'password' => Hash::make(Str::random(16)), 
            'role' => 'teacher',
            'is_approved' => 0,
            'is_active' => 0,
        ]);

        return redirect()->route('teacher.pending');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && !$user->hasVerifiedEmail() && $user->role === 'student') {
            $user->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Has salido del sistema.');
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