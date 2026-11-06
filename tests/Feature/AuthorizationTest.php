<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_access_admin_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_unverified_admin_is_sent_to_email_verification(): void
    {
        $admin = User::factory()->unverified()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/users')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_teacher_cannot_access_admin_panel(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)->get('/admin/users')->assertForbidden();
    }

    public function test_non_admin_cannot_use_sensitive_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->post('/admin/system/env/update')->assertForbidden();
        $this->actingAs($user)->post('/admin/system/backup')->assertForbidden();
        $this->actingAs($user)->post('/admin/system/logs/view')->assertForbidden();
        $this->actingAs($user)->post('/admin/system/maintenance')->assertForbidden();
        $this->actingAs($user)->get('/admin/check-imap')->assertForbidden();
    }

    public function test_non_admin_cannot_change_roles(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $other = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)
            ->put("/admin/users/{$other->id}/role", ['role' => 'admin'])
            ->assertForbidden();

        $this->assertEquals('student', $other->fresh()->role);
    }

    public function test_non_admin_cannot_delete_users(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $other = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->delete("/admin/users/{$other->id}")->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $other->id]);
    }

    public function test_non_admin_cannot_approve_teachers(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher', 'is_approved' => false]);

        $this->actingAs($user)->post("/admin/teachers/{$teacher->id}/approve")->assertForbidden();

        $this->assertFalse((bool) $teacher->fresh()->is_approved);
    }
}