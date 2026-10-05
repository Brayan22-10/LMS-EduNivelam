<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function create()
    {
        return view('page.contact');
    }

    public function store(Request $request)
    {
        $rateLimitKey = 'public-contact-' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()->withInput()->with('error', 'Has enviado demasiados mensajes. Por favor espera ' . ceil($seconds / 60) . ' minutos antes de intentar de nuevo.');
        }

        RateLimiter::hit($rateLimitKey, 900);

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        try {
            Mail::to('edunivelamsoporte@gmail.com')
            ->send(new ContactMessage(
                $validated['name'],
                $validated['email'],
                $validated['subject'],
                $validated['message']
            ));

            return back()->with('success', '¡Mensaje enviado correctamente! Nuestro equipo de soporte académico se pondrá en contacto contigo pronto.');
            
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ocurrió un error al enviar el correo. Por favor, inténtalo más tarde.');
        }
    }
}