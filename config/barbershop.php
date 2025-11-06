<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Barbershop Information
    |--------------------------------------------------------------------------
    */

    'name' => env('BARBERSHOP_NAME', 'Premium Barbershop'),
    'phone' => env('BARBERSHOP_PHONE', '+1234567890'),
    'email' => env('BARBERSHOP_EMAIL', 'contact@barbershoppremium.com'),
    'address' => env('BARBERSHOP_ADDRESS', '123 Main Street, City, State 12345'),

    /*
    |--------------------------------------------------------------------------
    | Business Hours
    |--------------------------------------------------------------------------
    */

    'business_hours' => [
        'start' => env('BUSINESS_HOURS_START', '09:00'),
        'end' => env('BUSINESS_HOURS_END', '19:00'),
        'days' => explode(',', env('BUSINESS_DAYS', '1,2,3,4,5,6')), // 0=Sunday
    ],

    /*
    |--------------------------------------------------------------------------
    | Appointment Settings
    |--------------------------------------------------------------------------
    */

    'appointment_duration_default' => (int) env('APPOINTMENT_DURATION_DEFAULT', 30),
    'appointment_buffer_time' => (int) env('APPOINTMENT_BUFFER_TIME', 15),
    'appointment_max_advance_days' => (int) env('APPOINTMENT_MAX_ADVANCE_DAYS', 30),
    'appointment_reminder_hours' => (int) env('APPOINTMENT_REMINDER_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'per_minute' => (int) env('RATE_LIMIT_PER_MINUTE', 60),
        'appointments_per_day' => (int) env('RATE_LIMIT_APPOINTMENTS_PER_DAY', 5),
    ],
];
