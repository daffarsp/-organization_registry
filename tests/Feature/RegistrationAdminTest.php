<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_filament_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_filament_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_registrations_resource(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $registration = Registration::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/registrations');

        $response->assertStatus(200);
        $response->assertSee($registration->name);
    }

    public function test_admin_cannot_access_registration_approval_edit_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);
        $registration = Registration::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/registrations/{$registration->registration_number}/edit");

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_registration_approval_and_admin_logs(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);
        $registration = Registration::factory()->create();

        $this->actingAs($superAdmin)
            ->get("/admin/registrations/{$registration->registration_number}/edit")
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/admin-activity-logs')
            ->assertStatus(200);
    }

    public function test_super_admin_cannot_create_questions(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)->get('/admin/questions/create');

        $response->assertStatus(403);
    }

    public function test_admin_can_manage_screening_questions(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/screening-questions')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/screening-questions/create')
            ->assertStatus(200);
    }

    public function test_super_admin_cannot_create_screening_questions(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get('/admin/screening-questions')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/screening-questions/create')
            ->assertStatus(403);
    }

    public function test_super_admin_can_manage_panel_accounts(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get('/admin/users')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/users/create')
            ->assertStatus(200);
    }

    public function test_admin_cannot_manage_panel_accounts(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertStatus(403);
    }
}
