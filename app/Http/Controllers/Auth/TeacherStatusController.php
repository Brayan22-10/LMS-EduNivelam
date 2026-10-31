<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class TeacherStatusController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 900; 

    private const TYPES = [
        'courses'    => 'Creación de cursos',
        'advisories' => 'Asesorías',
    ];

    public function show()
    {
        return view('auth.teacher-status');
    }

    public function check(Request $request)
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'dni'   => ['required', 'string', 'regex:/^[0-9]+$/', 'max:30'],
        ], [
            'email.required' => 'Ingresa el correo con el que te postulaste.',
            'email.email'    => 'Ingresa un correo válido.',
            'dni.required'   => 'Ingresa tu número de documento.',
            'dni.regex'      => 'El documento solo debe contener números.',
        ]);

        $key = 'teacher-status|' . $data['email'] . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            return back()->withInput($request->only('email'))->withErrors([
                'email' => "Demasiadas consultas. Inténtalo de nuevo en {$minutes} minuto(s).",
            ]);
        }

        $teacher = User::where('role', 'teacher')
            ->where('email', $data['email'])
            ->where('dni', $data['dni'])
            ->first();

        if (! $teacher) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'No encontramos una solicitud con esos datos. Revisa tu correo y tu documento.',
            ]);
        }

        RateLimiter::clear($key);

        $approved = (bool) $teacher->is_approved;
        $position = null;
        $total    = null;

        if (! $approved) {
            $pending = User::where('role', 'teacher')->where('is_approved', false);

            $total = (clone $pending)->count();

            $position = (clone $pending)->where(function ($q) use ($teacher) {
                $q->where('created_at', '<', $teacher->created_at)
                  ->orWhere(function ($q2) use ($teacher) {
                      $q2->where('created_at', $teacher->created_at)
                         ->where('id', '<', $teacher->id);
                  });
            })->count() + 1;
        }

        $result = [
            'name'     => Str::of($teacher->name)->before(' ')->toString(),
            'status'   => $approved ? 'approved' : 'pending',
            'position' => $position,
            'total'    => $total,
            'sent_at'  => optional($teacher->created_at)->format('d/m/Y H:i'),
            'type'     => self::TYPES[$teacher->teaching_type] ?? 'Docencia',
        ];

        return redirect()->route('teacher.status')
            ->with('result', $result)
            ->withInput(['email' => $data['email']]);
    }
}