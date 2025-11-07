<?php

namespace Tests\Unit\DTOs;

use App\DTOs\AvailabilityData;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AvailabilityDataTest extends TestCase
{
    public function test_can_create_availability_data_with_valid_data(): void
    {
        $date = Carbon::today();

        $data = new AvailabilityData(
            barberId: 5,
            date: $date,
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );

        $this->assertEquals(5, $data->barberId);
        $this->assertEquals($date, $data->date);
        $this->assertEquals('09:00', $data->startTime);
        $this->assertEquals('17:00', $data->endTime);
        $this->assertTrue($data->isAvailable);
        $this->assertEquals(30, $data->duration);
    }

    public function test_throws_exception_for_invalid_barber_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Barber ID must be positive');

        new AvailabilityData(
            barberId: 0,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );
    }

    public function test_throws_exception_for_invalid_start_time_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Start time must be in H:i format');

        new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '9:00 AM',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );
    }

    public function test_throws_exception_for_invalid_end_time_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End time must be in H:i format');

        new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '5:00 PM',
            isAvailable: true,
            duration: 30
        );
    }

    public function test_throws_exception_for_non_positive_duration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Duration must be positive');

        new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 0
        );
    }

    public function test_throws_exception_when_end_time_before_start_time(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End time must be after start time');

        new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '17:00',
            endTime: '09:00',
            isAvailable: true,
            duration: 30
        );
    }

    public function test_can_create_from_array(): void
    {
        $data = AvailabilityData::fromArray([
            'barber_id' => 5,
            'date' => '2024-01-15',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_available' => true,
            'duration' => 30,
        ]);

        $this->assertEquals(5, $data->barberId);
        $this->assertEquals('09:00', $data->startTime);
        $this->assertEquals('17:00', $data->endTime);
        $this->assertTrue($data->isAvailable);
        $this->assertEquals(30, $data->duration);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $date = Carbon::parse('2024-01-15');

        $data = new AvailabilityData(
            barberId: 5,
            date: $date,
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );

        $array = $data->toArray();

        $this->assertEquals(5, $array['barber_id']);
        $this->assertEquals('2024-01-15', $array['date']);
        $this->assertEquals('09:00', $array['start_time']);
        $this->assertEquals('17:00', $array['end_time']);
        $this->assertTrue($array['is_available']);
        $this->assertEquals(30, $array['duration']);
    }

    public function test_has_availability_returns_true_when_available(): void
    {
        $data = new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );

        $this->assertTrue($data->hasAvailability());
    }

    public function test_has_availability_returns_false_when_not_available(): void
    {
        $data = new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: false,
            duration: 30
        );

        $this->assertFalse($data->hasAvailability());
    }

    public function test_get_total_minutes_calculates_correctly(): void
    {
        $data = new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );

        $this->assertEquals(480, $data->getTotalMinutes()); // 8 hours = 480 minutes
    }

    public function test_get_possible_appointments_calculates_correctly(): void
    {
        $data = new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );

        $this->assertEquals(16, $data->getPossibleAppointments()); // 480 / 30 = 16
    }

    public function test_get_possible_appointments_returns_zero_when_not_available(): void
    {
        $data = new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: false,
            duration: 30
        );

        $this->assertEquals(0, $data->getPossibleAppointments());
    }

    public function test_get_time_range_string_formats_correctly(): void
    {
        $data = new AvailabilityData(
            barberId: 5,
            date: Carbon::today(),
            startTime: '09:00',
            endTime: '17:00',
            isAvailable: true,
            duration: 30
        );

        $this->assertEquals('09:00 - 17:00', $data->getTimeRangeString());
    }
}
