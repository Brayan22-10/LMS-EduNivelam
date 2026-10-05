<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feedbackMessage;
    public $user;

    public function __construct(string $feedbackMessage, User $user)
    {
        $this->feedbackMessage = $feedbackMessage;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Comentario / Sugerencia de Usuario - EduNivelam',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback',
        );
    }
}