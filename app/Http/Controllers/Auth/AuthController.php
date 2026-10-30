<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS  = 5;
    private const BASE_LOCKOUT  = 300;   
    private const MAX_LOCKOUT   = 3600; 

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = Str::lower(trim((string) $request->input('email')));
        $throttleKey = $email . '|' . $request->ip();
        $lockoutCacheKey = 'lockout_count_' . $throttleKey;

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            return back()->withInput()->withErrors([
                'email' => 'Demasiados intentos fallidos. Espera a que termine el bloqueo para volver a intentarlo.',
            ]);
        }

        $request->merge(['email' => $email]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'regex:/^\S*$/'],
        ], [
            'password.regex' => 'La contraseña no puede contener espacios en blanco.',
        ]);

        $remember = $request->boolean('remember');
        $user = User::where('email', $email)->first();

        if ($user) {
            $passwordOk = Hash::check($credentials['password'], $user->password);
        } else {
            Hash::make(Str::random(16));
            $passwordOk = false;
        }

        if ($passwordOk) {
            if ($user->role === 'teacher' && ! $user->is_approved) {
                return back()->withErrors([
                    'email' => 'Tu cuenta de docente se encuentra pendiente de aprobación por el administrador.',
                ])->onlyInput('email');
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

                return back()->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada por un administrador. No puedes iniciar sesión.',
                ])->onlyInput('email');
            }

            if (Auth::attempt($credentials, $remember)) {
                RateLimiter::clear($throttleKey);
                cache()->forget($lockoutCacheKey);

                $request->session()->regenerate();

                $this->securityLog(
                    $request,
                    Auth::id(),
                    Auth::user()->email,
                    'success',
                    'successful_login',
                    'Inicio de sesión exitoso para el usuario ID: ' . Auth::id()
                );

                if ($remember) {
                    Cookie::queue('saved_email', $email, 43200);
                    Cookie::queue('remember_checked', 'true', 43200);
                } else {
                    Cookie::queue(Cookie::forget('saved_email'));
                    Cookie::queue(Cookie::forget('remember_checked'));
                }
                Cookie::queue(Cookie::forget('saved_password'));

                return redirect()->intended(route('home'));
            }
        }

        RateLimiter::hit($throttleKey, self::BASE_LOCKOUT);

        $this->securityLog(
            $request,
            $user ? $user->id : null,
            $email,
            'failed',
            'failed_login',
            "Intento de acceso fallido para el correo: {$email} desde la IP: {$request->ip()}"
        );

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $blockCount = cache()->get($lockoutCacheKey, 0) + 1;
            cache()->put($lockoutCacheKey, $blockCount, now()->addHours(24));

            $lockoutSeconds = min(
                self::BASE_LOCKOUT * (2 ** min($blockCount - 1, 6)),
                self::MAX_LOCKOUT
            );

            RateLimiter::clear($throttleKey);
            for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
                RateLimiter::hit($throttleKey, $lockoutSeconds);
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son válidas.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request): \Illuminate\Http\RedirectResponse
    {
        $email = Str::lower(trim((string) $request->input('email')));
        $dni = $request->input('dni');

        $request->merge(['email' => $email]);

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'dni' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:30'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'regex:/^\S*$/'],
        ], [
            'dni.required' => 'La cédula o documento es obligatorio.',
            'dni.regex' => 'La identificación solo debe contener números.',
            'phone.regex' => 'El teléfono solo debe contener números.',
            'password.regex' => 'La contraseña no puede contener espacios en blanco.',
        ]);
        
        $existingVerified = User::where(function($query) use ($email, $dni) {
            $query->where('email', $email)->orWhere('dni', $dni);
        })->whereNotNull('email_verified_at')->first();

        if ($existingVerified) {
            return back()->withInput()->withErrors([
                'email' => 'Este correo electrónico o número de documento ya se encuentra registrado y verificado.',
            ]);
        }

        User::where(function($query) use ($email, $dni) {
            $query->where('email', $email)->orWhere('dni', $dni);
        })->whereNull('email_verified_at')->delete();

        $user = User::create([
            'name' => $request->string('name'),
            'dni' => $dni,
            'phone' => $request->filled('phone') ? $request->input('phone') : null,
            'email' => $email,
            'password' => Hash::make($request->string('password')),
            'role' => 'student',
            'is_approved' => true,
            'is_active' => true,
            'font_size' => '14px',
            'locale' => 'es',
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
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

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
            'email' => $request->input('email'),
            'dni' => $request->input('dni'),
            'country_code' => $request->input('country_code'),
            'phone' => $request->input('phone'),
            'birthdate' => $request->input('birthdate'),
            'teaching_type' => $request->input('teaching_type'),
            'password' => Hash::make(Str::random(16)),
            'role' => 'teacher',
            'is_approved' => 0,
            'is_active' => 0,
            'font_size' => '14px',
            'locale' => 'es',
        ]);

        return redirect()->route('teacher.pending');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && ! $user->hasVerifiedEmail() && $user->role === 'student') {
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
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            $code = random_int(100000, 999999);

            $user->reset_code = $code;
            $user->reset_code_expires_at = Carbon::now()->addMinutes(15);
            $user->save();

            try {
                Mail::raw("Tu código de recuperación para EduNivelam es: {$code}", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Código de recuperación de contraseña - EduNivelam');
                });
            } catch (Throwable $e) {
                Log::error('No se pudo enviar el código de recuperación: ' . $e->getMessage());
            }
        }

        session(['reset_email' => $request->input('email')]);
        session()->forget('code_verified');

        return redirect()->route('password.verify.form')
            ->with('success', 'Si el correo está registrado, recibirás un código de 6 dígitos.');
    }

    public function showVerifyForm()
    {
        if (! session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $user = User::where('email', session('reset_email'))
            ->where('reset_code', $request->code)
            ->where('reset_code_expires_at', '>', Carbon::now())
            ->first();

        if (! $user) {
            return back()->withErrors(['code' => 'El código es inválido o ha expirado.']);
        }

        session(['code_verified' => true]);

        return redirect()->route('password.reset.form');
    }

    public function showResetForm()
    {
        if (! session('reset_email') || ! session('code_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function updatePassword(Request $request)
    {
        if (! session('reset_email') || ! session('code_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'regex:/^\S*$/', 'confirmed'],
        ], [
            'password.regex' => 'La contraseña no puede contener espacios en blanco.',
        ]);

        $user = User::where('email', session('reset_email'))->first();

        if (! $user
            || ! $user->reset_code_expires_at
            || Carbon::parse($user->reset_code_expires_at)->isPast()) {
            session()->forget(['reset_email', 'code_verified']);

            return redirect()->route('password.request')
                ->withErrors(['email' => 'El código expiró. Solicita uno nuevo.']);
        }

        $user->password = Hash::make($request->password);
        $user->reset_code = null;
        $user->reset_code_expires_at = null;
        $user->save();

        session()->forget(['reset_email', 'code_verified']);

        return redirect()->route('login')
            ->with('success', '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.');
    }

    private function securityLog(Request $request, ?int $userId, string $email, string $status, string $event, string $description): void
    {
        DB::table('security_logs')->insert([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $request->ip(),
            'status' => $status,
            'event_type' => $event,
            'description' => $description,
            'user_agent' => $request->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}