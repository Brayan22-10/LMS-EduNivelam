<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceivedEmail;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;

class ReceivedEmailController extends Controller
{
    public function index()
    {
        $emails = ReceivedEmail::latest('received_at')->paginate(10);
        return view('admin.emails.index', compact('emails'));
    }

    public function show($id)
    {
        $email = ReceivedEmail::findOrFail($id);

        if (!$email->is_read) {
            $email->update(['is_read' => true]);
        }

        $rawBody = $email->body ?? $email->message ?? '';
        
        if (str_contains($rawBody, 'Mensaje del usuario:')) {
            $parts = explode('Mensaje del usuario:', $rawBody);
            $userMessage = strip_tags($parts[1] ?? '');
        } else {
            $userMessage = strip_tags($rawBody);
        }

        $cleaned = trim($userMessage);
        $cleaned = str_replace('Este es un correo automatizado generado desde el sistema EduNivelam. Por favor no respondas directamente a este mensaje sin contactar al usuario por su correo institucional.', '', $cleaned);
        
        $email->clean_message = trim($cleaned);

        return view('admin.emails.show', compact('email'));
    }

    public function destroy($id)
    {
        try {
            $email = ReceivedEmail::findOrFail($id);
            $email->delete();

            return redirect()->route('admin.emails.index')->with('success', '¡El correo ha sido eliminado de la bandeja exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar el correo: ' . $e->getMessage());
        }
    }

    public function reply(Request $request, $id)
    {
        $rateLimitKey = 'admin-reply-' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()->with('error', 'Demasiados intentos de respuesta. Por favor espera ' . ceil($seconds / 60) . ' minutos.');
        }

        RateLimiter::hit($rateLimitKey, 600);

        $request->validate([
            'reply_message' => ['required', 'string', 'max:2000'],
        ], [
            'reply_message.required' => 'El mensaje de respuesta no puede estar vacío.',
            'reply_message.max' => 'El mensaje no puede superar los 2000 caracteres.'
        ]);

        $email = ReceivedEmail::findOrFail($id);
        
        $existsUser = User::where('email', $email->from_email)->exists();

        if (!$existsUser) {
            return back()->with('error', 'Seguridad activada: El remitente no pertenece a un usuario registrado válido en la plataforma.');
        }

        $replyContent = $request->input('reply_message');
        $recipientEmail = $email->from_email;

        try {
            Mail::raw($replyContent, function ($message) use ($recipientEmail, $email) {
                $message->to($recipientEmail)
                        ->subject('Respuesta de Soporte: ' . ($email->subject ?? 'EduNivelam'));
            });

            return back()->with('success', '¡Respuesta enviada exitosamente a ' . $recipientEmail . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Fallo al enviar la respuesta por correo: ' . $e->getMessage());
        }
    }
}