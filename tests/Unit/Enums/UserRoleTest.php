<?php

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_has_all_required_cases(): void
    {
        $cases = UserRole::cases();
        $this->assertCount(3, $cases);

        $values = array_map(fn ($case) => $case->value, $cases);
        $this->assertContains('admin', $values);
        $this->assertContains('barber', $values);
        $this->assertContains('customer', $values);
    }

    public function test_label_returns_correct_string(): void
    {
        $this->assertEquals('Administrator', UserRole::ADMIN->label());
        $this->assertEquals('Barber', UserRole::BARBER->label());
        $this->assertEquals('Customer', UserRole::CUSTOMER->label());
    }

    public function test_can_manage_appointments_returns_true_for_admin_and_barber(): void
    {
        $this->assertTrue(UserRole::ADMIN->canManageAppointments());
        $this->assertTrue(UserRole::BARBER->canManageAppointments());
    }

    public function test_can_manage_appointments_returns_false_for_customer(): void
    {
        $this->assertFalse(UserRole::CUSTOMER->canManageAppointments());
    }

    public function test_can_manage_barbers_returns_true_only_for_admin(): void
    {
        $this->assertTrue(UserRole::ADMIN->canManageBarbers());
        $this->assertFalse(UserRole::BARBER->canManageBarbers());
        $this->assertFalse(UserRole::CUSTOMER->canManageBarbers());
    }

    public function test_can_manage_services_returns_true_only_for_admin(): void
    {
        $this->assertTrue(UserRole::ADMIN->canManageServices());
        $this->assertFalse(UserRole::BARBER->canManageServices());
        $this->assertFalse(UserRole::CUSTOMER->canManageServices());
    }

    public function test_is_admin_returns_true_only_for_admin(): void
    {
        $this->assertTrue(UserRole::ADMIN->isAdmin());
        $this->assertFalse(UserRole::BARBER->isAdmin());
        $this->assertFalse(UserRole::CUSTOMER->isAdmin());
    }

    public function test_is_barber_returns_true_only_for_barber(): void
    {
        $this->assertFalse(UserRole::ADMIN->isBarber());
        $this->assertTrue(UserRole::BARBER->isBarber());
        $this->assertFalse(UserRole::CUSTOMER->isBarber());
    }

    public function test_is_customer_returns_true_only_for_customer(): void
    {
        $this->assertFalse(UserRole::ADMIN->isCustomer());
        $this->assertFalse(UserRole::BARBER->isCustomer());
        $this->assertTrue(UserRole::CUSTOMER->isCustomer());
    }

    public function test_permissions_returns_array(): void
    {
        $this->assertIsArray(UserRole::ADMIN->permissions());
        $this->assertIsArray(UserRole::BARBER->permissions());
        $this->assertIsArray(UserRole::CUSTOMER->permissions());
    }

    public function test_admin_has_most_permissions(): void
    {
        $adminPerms = UserRole::ADMIN->permissions();
        $barberPerms = UserRole::BARBER->permissions();
        $customerPerms = UserRole::CUSTOMER->permissions();

        $this->assertGreaterThan(count($barberPerms), count($adminPerms));
        $this->assertGreaterThan(count($customerPerms), count($adminPerms));
    }

    public function test_admin_permissions_include_manage_capabilities(): void
    {
        $permissions = UserRole::ADMIN->permissions();

        $this->assertContains('manage_appointments', $permissions);
        $this->assertContains('manage_barbers', $permissions);
        $this->assertContains('manage_services', $permissions);
    }

    public function test_barber_permissions_include_appointment_management(): void
    {
        $permissions = UserRole::BARBER->permissions();

        $this->assertContains('view_own_appointments', $permissions);
        $this->assertContains('manage_own_schedule', $permissions);
    }

    public function test_customer_permissions_include_booking(): void
    {
        $permissions = UserRole::CUSTOMER->permissions();

        $this->assertContains('book_appointment', $permissions);
        $this->assertContains('view_own_appointments', $permissions);
        $this->assertContains('cancel_own_appointment', $permissions);
    }
}
