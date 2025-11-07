<?php

namespace Tests\Unit\DTOs;

use App\DTOs\AppointmentData;
use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AppointmentDataTest extends TestCase
{
    public function test_can_create_appointment_data_with_valid_data(): void
    {
        $now = Carbon::now();
        $endTime = $now->copy()->addMinutes(30);

        $data = new AppointmentData(
            id: 1,
            customerId: 10,
            barberId: 5,
            serviceId: 3,
            appointmentDate: $now,
            startTime: $now,
            endTime: $endTime,
            status: AppointmentStatus::PENDING,
            notes: 'Test notes',
            createdAt: $now,
            updatedAt: $now
        );

        $this->assertEquals(1, $data->id);
        $this->assertEquals(10, $data->customerId);
        $this->assertEquals(5, $data->barberId);
        $this->assertEquals(3, $data->serviceId);
        $this->assertEquals($now, $data->appointmentDate);
        $this->assertEquals(AppointmentStatus::PENDING, $data->status);
        $this->assertEquals('Test notes', $data->notes);
    }

    public function test_can_create_appointment_data_without_optional_fields(): void
    {
        $now = Carbon::now();
        $endTime = $now->copy()->addMinutes(30);

        $data = new AppointmentData(
            id: null,
            customerId: 10,
            barberId: 5,
            serviceId: 3,
            appointmentDate: $now,
            startTime: $now,
            endTime: $endTime,
            status: AppointmentStatus::PENDING
        );

        $this->assertNull($data->id);
        $this->assertNull($data->notes);
        $this->assertNull($data->createdAt);
        $this->assertNull($data->updatedAt);
    }

    public function test_throws_exception_for_invalid_customer_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer ID must be positive');

        $now = Carbon::now();
        new AppointmentData(
            id: 1,
            customerId: 0,
            barberId: 5,
            serviceId: 3,
            appointmentDate: $now,
            startTime: $now,
            endTime: $now->copy()->addMinutes(30),
            status: AppointmentStatus::PENDING
        );
    }

    public function test_throws_exception_for_invalid_barber_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Barber ID must be positive');

        $now = Carbon::now();
        new AppointmentData(
            id: 1,
            customerId: 10,
            barberId: -1,
            serviceId: 3,
            appointmentDate: $now,
            startTime: $now,
            endTime: $now->copy()->addMinutes(30),
            status: AppointmentStatus::PENDING
        );
    }

    public function test_throws_exception_for_invalid_service_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service ID must be positive');

        $now = Carbon::now();
        new AppointmentData(
            id: 1,
            customerId: 10,
            barberId: 5,
            serviceId: 0,
            appointmentDate: $now,
            startTime: $now,
            endTime: $now->copy()->addMinutes(30),
            status: AppointmentStatus::PENDING
        );
    }

    public function test_throws_exception_when_end_time_before_start_time(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End time must be after start time');

        $now = Carbon::now();
        new AppointmentData(
            id: 1,
            customerId: 10,
            barberId: 5,
            serviceId: 3,
            appointmentDate: $now,
            startTime: $now,
            endTime: $now->copy()->subMinutes(10),
            status: AppointmentStatus::PENDING
        );
    }

    public function test_can_create_from_array(): void
    {
        $data = AppointmentData::fromArray([
            'id' => 1,
            'customer_id' => 10,
            'barber_id' => 5,
            'service_id' => 3,
            'scheduled_at' => '2024-01-15 10:00:00',
            'duration_minutes' => 30,
            'status' => 'pending',
            'notes' => 'Test notes',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00',
        ]);

        $this->assertEquals(1, $data->id);
        $this->assertEquals(10, $data->customerId);
        $this->assertEquals(5, $data->barberId);
        $this->assertEquals(3, $data->serviceId);
        $this->assertEquals(AppointmentStatus::PENDING, $data->status);
        $this->assertEquals('Test notes', $data->notes);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $now = Carbon::parse('2024-01-15 10:00:00');
        $endTime = $now->copy()->addMinutes(30);

        $data = new AppointmentData(
            id: 1,
            customerId: 10,
            barberId: 5,
            serviceId: 3,
            appointmentDate: $now,
            startTime: $now,
            endTime: $endTime,
            status: AppointmentStatus::PENDING,
            notes: 'Test notes',
            createdAt: $now,
            updatedAt: $now
        );

        $array = $data->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertEquals(10, $array['customer_id']);
        $this->assertEquals(5, $array['barber_id']);
        $this->assertEquals(3, $array['service_id']);
        $this->assertEquals('2024-01-15 10:00:00', $array['scheduled_at']);
        $this->assertEquals(30, $array['duration_minutes']);
        $this->assertEquals('pending', $array['status']);
        $this->assertEquals('Test notes', $array['notes']);
    }
}
