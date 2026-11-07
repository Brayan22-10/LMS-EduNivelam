<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function datosRegistro(array $override = []): array
    {
        return array_merge([
            'name' => 'Usuario Prueba',
            'dni' => '1020304050',
            'phone' => '3001234567',
            'email' => 'prueba@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ], $override);
    }

    public function test_login_page_is_available(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', $this->datosRegistro());

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'prueba@example.com']);
        $this->assertAuthenticated();
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'repetido@example.com', 'dni' => '999']);

        $response = $this->post('/register', $this->datosRegistro([
            'email' => 'repetido@example.com',
        ]));

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_short_password_is_rejected(): void
    {
        $response = $this->post('/register', $this->datosRegistro([
            'password' => 'Ab1',
            'password_confirmation' => 'Ab1',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'prueba@example.com']);
    }

    public function test_weak_password_is_rejected(): void
    {
        $response = $this->post('/register', $this->datosRegistro([
            'password' => 'aaaaaaaa',
            'password_confirmation' => 'aaaaaaaa',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'prueba@example.com']);
    }

    public function test_password_confirmation_mismatch_is_rejected(): void
    {
        $response = $this->post('/register', $this->datosRegistro([
            'password_confirmation' => 'OtraCosa999',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'prueba@example.com']);
    }

    public function test_password_is_stored_hashed(): void
    {
        $this->post('/register', $this->datosRegistro());

        $user = User::where('email', 'prueba@example.com')->first();

        $this->assertNotNull($user);
        $this->assertNotEquals('Password123', $user->password);
        $this->assertTrue(Hash::check('Password123', $user->password));
    }

    public function test_registration_cannot_assign_admin_role(): void
    {
        $this->post('/register', $this->datosRegistro(['role' => 'admin']));

        $this->assertDatabaseHas('users', [
            'email' => 'prueba@example.com',
            'role' => 'student',
        ]);
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
