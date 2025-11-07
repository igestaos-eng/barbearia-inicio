<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case BARBER = 'barber';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::BARBER => 'Barber',
            self::CUSTOMER => 'Customer',
        };
    }

    public function canManageAppointments(): bool
    {
        return in_array($this, [self::ADMIN, self::BARBER]);
    }

    public function canManageBarbers(): bool
    {
        return $this === self::ADMIN;
    }

    public function canManageServices(): bool
    {
        return $this === self::ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isBarber(): bool
    {
        return $this === self::BARBER;
    }

    public function isCustomer(): bool
    {
        return $this === self::CUSTOMER;
    }

    /**
     * @return array<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                'manage_all',
                'view_all',
                'edit_all',
                'delete_all',
                'manage_appointments',
                'manage_barbers',
                'manage_services',
                'manage_users',
                'manage_settings',
            ],
            self::BARBER => [
                'view_own_appointments',
                'manage_own_schedule',
                'view_customers',
                'update_appointment_status',
            ],
            self::CUSTOMER => [
                'book_appointment',
                'view_own_appointments',
                'cancel_own_appointment',
            ],
        };
    }
}
