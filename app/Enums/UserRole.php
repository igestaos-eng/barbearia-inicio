<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case BARBER = 'barber';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::BARBER => 'Barber',
            self::CUSTOMER => 'Customer',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SUPERADMIN => ['manage_all', 'view_all', 'edit_all', 'delete_all', 'manage_admins', 'manage_settings'],
            self::ADMIN => ['manage_all', 'view_all', 'edit_all', 'delete_all'],
            self::BARBER => ['view_own_appointments', 'manage_own_schedule', 'view_customers'],
            self::CUSTOMER => ['book_appointment', 'view_own_appointments', 'cancel_own_appointment'],
        };
    }
}
