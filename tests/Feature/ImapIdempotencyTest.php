<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ReceivedEmail;
use Carbon\Carbon;

class ImapIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_prevents_duplicate_emails_using_message_id()
    {
        $emailData = [
            'message_id' => 'unique-msg-id-999',
            'from_email' => 'remitente@test.com',
            'from_name' => 'Remitente',
            'subject' => 'Prueba Idempotencia',
            'body' => 'Contenido de prueba',
            'received_at' => Carbon::now(),
            'is_read' => false,
        ];

        ReceivedEmail::firstOrCreate(
            ['message_id' => $emailData['message_id']],
            $emailData
        );

        ReceivedEmail::firstOrCreate(
            ['message_id' => $emailData['message_id']],
            $emailData
        );

        $this->assertDatabaseCount('received_emails', 1);
    }
}