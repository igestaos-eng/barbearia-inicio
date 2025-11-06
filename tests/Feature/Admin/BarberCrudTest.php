<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Barber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarberCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can view barbers list.
     */
    public function test_admin_can_view_barbers_list(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $barberUser = User::factory()->create(['role' => UserRole::BARBER]);
        $barber = Barber::factory()->create(['user_id' => $barberUser->id]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.barbers.index'));

        $response->assertStatus(200);
        $response->assertSee($barberUser->name);
    }

    /**
     * Test admin can view create barber form.
     */
    public function test_admin_can_view_create_barber_form(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.barbers.create'));

        $response->assertStatus(200);
        $response->assertSee('Create New Barber');
    }

    /**
     * Test admin can create a new barber.
     */
    public function test_admin_can_create_new_barber(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $barberUser = User::factory()->create(['role' => UserRole::BARBER]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.barbers.store'), [
            'user_id' => $barberUser->id,
            'specialization' => 'Classic Cuts',
            'bio' => 'Experienced barber',
            'experience_years' => 5,
            'is_available' => true,
        ]);

        $response->assertRedirect(route('admin.barbers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('barbers', [
            'user_id' => $barberUser->id,
            'specialization' => 'Classic Cuts',
        ]);
    }

    /**
     * Test admin can view edit barber form.
     */
    public function test_admin_can_view_edit_barber_form(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $barberUser = User::factory()->create(['role' => UserRole::BARBER]);
        $barber = Barber::factory()->create(['user_id' => $barberUser->id]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.barbers.edit', $barber));

        $response->assertStatus(200);
        $response->assertSee('Edit Barber');
        $response->assertSee($barberUser->name);
    }

    /**
     * Test admin can update a barber.
     */
    public function test_admin_can_update_barber(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $barberUser = User::factory()->create(['role' => UserRole::BARBER]);
        $barber = Barber::factory()->create([
            'user_id' => $barberUser->id,
            'specialization' => 'Old Specialization',
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.barbers.update', $barber), [
            'specialization' => 'New Specialization',
            'bio' => 'Updated bio',
            'experience_years' => 10,
            'is_available' => false,
        ]);

        $response->assertRedirect(route('admin.barbers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('barbers', [
            'id' => $barber->id,
            'specialization' => 'New Specialization',
            'is_available' => false,
        ]);
    }

    /**
     * Test admin can delete a barber.
     */
    public function test_admin_can_delete_barber(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $barberUser = User::factory()->create(['role' => UserRole::BARBER]);
        $barber = Barber::factory()->create(['user_id' => $barberUser->id]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.barbers.destroy', $barber));

        $response->assertRedirect(route('admin.barbers.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('barbers', [
            'id' => $barber->id,
        ]);
    }

    /**
     * Test barber creation requires valid user_id.
     */
    public function test_barber_creation_requires_valid_user_id(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.barbers.store'), [
            'user_id' => 9999, // Non-existent user
            'specialization' => 'Classic Cuts',
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    /**
     * Test customer cannot create barber.
     */
    public function test_customer_cannot_create_barber(): void
    {
        $customer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $barberUser = User::factory()->create(['role' => UserRole::BARBER]);

        $this->actingAs($customer);

        $response = $this->post(route('admin.barbers.store'), [
            'user_id' => $barberUser->id,
            'specialization' => 'Classic Cuts',
        ]);

        $response->assertStatus(403);
    }
}
