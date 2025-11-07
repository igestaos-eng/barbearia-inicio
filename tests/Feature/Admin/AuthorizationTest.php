<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot access admin dashboard.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test customer cannot access admin dashboard.
     */
    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /**
     * Test admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test admin can access user management.
     */
    public function test_admin_can_access_user_management(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /**
     * Test admin can access barbers management.
     */
    public function test_admin_can_access_barbers_management(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.barbers.index'));

        $response->assertStatus(200);
    }

    /**
     * Test admin can access services management.
     */
    public function test_admin_can_access_services_management(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.services.index'));

        $response->assertStatus(200);
    }

    /**
     * Test admin can access appointments management.
     */
    public function test_admin_can_access_appointments_management(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.appointments.index'));

        $response->assertStatus(200);
    }
}
