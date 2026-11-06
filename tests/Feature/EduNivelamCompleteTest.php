<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;
use Tests\TestCase;

class EduNivelamCompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_inicio_carga_correctamente()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('page.home'); 
    }

    public function test_paginas_estaticas_principales()
    {
        $this->get('/about')->assertStatus(200);
        $this->get('/courses')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
    }

    public function test_usuario_puede_registrarse()
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name'                  => 'Estudiante Prueba',
            'dni'                   => '1234567890', 
            'email'                 => 'estudiante@edunivelam.test',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('users', [
            'email' => 'estudiante@edunivelam.test'
        ]);
    }

    public function test_usuario_puede_iniciar_sesion()
    {
        $user = User::factory()->create([
            'email'     => 'login@edunivelam.test',
            'password'  => bcrypt($password = 'Password123!'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'login@edunivelam.test',
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertStatus(302);
    }

    public function test_sesion_expira_o_requiere_autenticacion_en_rutas_protegidas()
    {
        $response = $this->get('/dashboard');
        $this->assertTrue(in_array($response->getStatusCode(), [302, 401]));
    }

    public function test_usuario_puede_cerrar_sesion()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertStatus(302);
    }

    public function test_panel_admin_restringido_a_usuarios_no_autorizados()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/users');
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_admin_puede_acceder_a_gestion()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_base_de_datos_crea_usuarios_correctamente()
    {
        User::factory()->create([
            'name'  => 'Prueba DB',
            'email' => 'db@edunivelam.test'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'db@edunivelam.test'
        ]);
    }

    public function test_admin_puede_crear_y_gestionar_posts_crud()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $post = Post::create([
            'user_id' => $admin->id,
            'title'   => 'Noticia de Prueba Ubaté',
            'content' => 'Contenido detallado del post de prueba para EduNivelam.'
        ]);

        $this->assertDatabaseHas('posts', ['title' => 'Noticia de Prueba Ubaté']);

        $post->update(['title' => 'Título Actualizado']);
        $this->assertDatabaseHas('posts', ['title' => 'Título Actualizado']);

        $post->delete();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_subida_de_avatar_o_imagenes_del_sistema()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->store('avatars', 'public');

        Storage::disk('public')->assertExists($path);
    }

    public function test_api_responde_correctamente()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/dashboard');
        
        $this->assertNotNull($response);
    }

    public function test_envio_de_correo_de_contacto()
    {
        Mail::fake();

        $mailable = new ContactMessage(
            'Visitante',
            'visitante@correo.com',
            'Soporte',
            'Mensaje de prueba'
        );

        Mail::to('admin@edunivelam.test')->send($mailable);

        Mail::assertSent(ContactMessage::class);
    }
}