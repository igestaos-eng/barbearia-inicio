<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login page can be displayed.
     */
    public function test_login_page_displays(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard Login');
    }

    /**
     * Test admin user can login with valid credentials.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test superadmin user can login with valid credentials.
     */
    public function test_superadmin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SUPERADMIN,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'superadmin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login fails with invalid credentials.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test non-admin user cannot login to admin panel.
     */
    public function test_customer_cannot_login_to_admin_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'customer@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test rate limiting on login attempts.
     */
    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // Clear any existing rate limits
        RateLimiter::clear('admin@test.com|127.0.0.1');

        // Make 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.post'), [
                'email' => 'admin@test.com',
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be rate limited
        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many', $response->getSession()->get('errors')->first('email'));
    }

    /**
     * Test admin can logout successfully.
     */
    public function test_admin_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    /**
     * Test remember me functionality works.
     */
    public function test_remember_me_functionality_works(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@test.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Check that remember token is set
        $this->assertNotNull($user->fresh()->remember_token);
    }
}
