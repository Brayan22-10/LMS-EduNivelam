<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create([
            'email' => 'castillo@edunivelam.test',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'castillo@edunivelam.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/'); 
    }

    public function test_users_cannot_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'castillo@edunivelam.test',
            'password' => bcrypt('password123')
        ]);

        $this->post('/login', [
            'email' => 'castillo@edunivelam.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}