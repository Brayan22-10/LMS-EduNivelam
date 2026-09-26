<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class SecurityAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_emails()
    {
        $user = User::factory()->create(); 

        $response = $this->actingAs($user)->get('/correos'); 
        
        $this->assertTrue(in_array($response->status(), [403, 404, 302])); 
    }
}