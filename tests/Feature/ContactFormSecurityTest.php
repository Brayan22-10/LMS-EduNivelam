<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class ContactFormSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_validation_fails_with_empty_data()
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => 'correo-invalido',
            'subject' => '',
            'message' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_contact_form_sends_email_successfully_with_valid_data()
    {
        Mail::fake();

        $response = $this->post('/contact', [
            'name' => 'Estudiante Test',
            'email' => 'estudiante@test.com',
            'subject' => 'Consulta de Nivelación',
            'message' => 'Necesito soporte con el curso.'
        ]);

        $response->assertStatus(302);
        
        Mail::assertSent(ContactMessage::class, function ($mail) {
            return $mail->hasTo('edunivelamsoporte@gmail.com');
        });
    }
}