<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_bio_is_sanitized(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/settings/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'bio' => '<script>alert(1)</script>Hola',
        ])->assertSessionHasNoErrors();

        $this->assertStringNotContainsString('<script>', (string) $user->fresh()->bio);
    }

    public function test_unknown_route_returns_404_without_stack_trace(): void
    {
        $this->get('/ruta-que-no-existe')
            ->assertNotFound()
            ->assertDontSee('vendor/laravel');
    }

    public function test_responses_include_security_headers(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy');
    }
}
