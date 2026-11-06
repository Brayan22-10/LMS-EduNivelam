<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    private function perfil(User $user, UploadedFile $archivo)
    {
        return $this->actingAs($user)->put('/settings/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $archivo,
        ]);
    }

    public function test_avatar_rejects_php_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->perfil($user, UploadedFile::fake()->create('shell.php', 10, 'application/x-php'))
            ->assertSessionHasErrors('avatar');

        $this->assertEmpty(Storage::disk('public')->files('avatars'));
    }

    public function test_avatar_rejects_svg_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->perfil($user, UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'))
            ->assertSessionHasErrors('avatar');
    }

    public function test_avatar_rejects_oversized_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->perfil($user, UploadedFile::fake()->image('grande.png')->size(20000))
            ->assertSessionHasErrors('avatar');
    }

    public function test_avatar_accepts_valid_image_with_random_name(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->perfil($user, UploadedFile::fake()->image('foto.png', 200, 200))
            ->assertSessionHasNoErrors();

        $files = Storage::disk('public')->files('avatars');
        $this->assertNotEmpty($files);
        $this->assertStringNotContainsString('foto', $files[0]);
    }
}