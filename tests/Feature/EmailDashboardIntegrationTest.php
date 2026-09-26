<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ReceivedEmail;
use Carbon\Carbon;

class EmailDashboardIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_received_emails_in_dashboard_index()
    {
        $admin = User::factory()->create([
            'email' => 'admin@edunivelam.edu.co'
        ]);

        ReceivedEmail::create([
            'message_id' => 'integration-msg-001',
            'from_email' => 'estudiante.pro@test.com',
            'from_name' => 'Estudiante Pro',
            'subject' => 'Error en plataforma',
            'body' => 'No puedo ver mis notas de nivelación.',
            'received_at' => Carbon::now(),
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.emails.index'));

        $response->assertStatus(200);
        $response->assertSee('Estudiante Pro');
        $response->assertSee('Error en plataforma');
        $response->assertSee('estudiante.pro@test.com');
    }
}