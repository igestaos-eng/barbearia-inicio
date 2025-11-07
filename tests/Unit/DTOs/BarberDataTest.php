<?php

namespace Tests\Unit\DTOs;

use App\DTOs\BarberData;
use App\Enums\ServiceType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BarberDataTest extends TestCase
{
    public function test_can_create_barber_data_with_valid_data(): void
    {
        $specializations = [ServiceType::HAIRCUT, ServiceType::BEARD];

        $data = new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: $specializations,
            rating: 4.5,
            totalAppointments: 100,
            bio: 'Experienced barber',
            isActive: true
        );

        $this->assertEquals(1, $data->id);
        $this->assertEquals(10, $data->userId);
        $this->assertEquals('John Doe', $data->name);
        $this->assertEquals('john@example.com', $data->email);
        $this->assertEquals('123-456-7890', $data->phone);
        $this->assertEquals($specializations, $data->specializations);
        $this->assertEquals(4.5, $data->rating);
        $this->assertEquals(100, $data->totalAppointments);
        $this->assertEquals('Experienced barber', $data->bio);
        $this->assertTrue($data->isActive);
    }

    public function test_can_create_barber_data_without_optional_fields(): void
    {
        $data = new BarberData(
            id: null,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: [],
            rating: 0.0,
            totalAppointments: 0
        );

        $this->assertNull($data->id);
        $this->assertNull($data->bio);
        $this->assertTrue($data->isActive); // Default value
    }

    public function test_throws_exception_for_invalid_user_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User ID must be positive');

        new BarberData(
            id: 1,
            userId: 0,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: [],
            rating: 0.0,
            totalAppointments: 0
        );
    }

    public function test_throws_exception_for_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');

        new BarberData(
            id: 1,
            userId: 10,
            name: '  ',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: [],
            rating: 0.0,
            totalAppointments: 0
        );
    }

    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'invalid-email',
            phone: '123-456-7890',
            specializations: [],
            rating: 0.0,
            totalAppointments: 0
        );
    }

    public function test_throws_exception_for_empty_phone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Phone cannot be empty');

        new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '  ',
            specializations: [],
            rating: 0.0,
            totalAppointments: 0
        );
    }

    public function test_throws_exception_for_invalid_specializations(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Specializations must be ServiceType enum instances');

        new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: ['invalid'],
            rating: 0.0,
            totalAppointments: 0
        );
    }

    public function test_throws_exception_for_rating_out_of_range(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rating must be between 0.0 and 5.0');

        new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: [],
            rating: 6.0,
            totalAppointments: 0
        );
    }

    public function test_throws_exception_for_negative_total_appointments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Total appointments cannot be negative');

        new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: [],
            rating: 0.0,
            totalAppointments: -1
        );
    }

    public function test_can_create_from_array(): void
    {
        $data = BarberData::fromArray([
            'id' => 1,
            'user_id' => 10,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'specializations' => ['haircut', 'beard'],
            'rating' => 4.5,
            'total_appointments' => 100,
            'bio' => 'Experienced barber',
            'is_active' => true,
        ]);

        $this->assertEquals(1, $data->id);
        $this->assertEquals(10, $data->userId);
        $this->assertEquals('John Doe', $data->name);
        $this->assertEquals(4.5, $data->rating);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $specializations = [ServiceType::HAIRCUT, ServiceType::BEARD];

        $data = new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: $specializations,
            rating: 4.5,
            totalAppointments: 100,
            bio: 'Experienced barber',
            isActive: true
        );

        $array = $data->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertEquals(10, $array['user_id']);
        $this->assertEquals('John Doe', $array['name']);
        $this->assertEquals('john@example.com', $array['email']);
        $this->assertEquals(['haircut', 'beard'], $array['specializations']);
        $this->assertEquals(4.5, $array['rating']);
        $this->assertEquals(100, $array['total_appointments']);
    }

    public function test_get_specializations_string_returns_formatted_string(): void
    {
        $specializations = [ServiceType::HAIRCUT, ServiceType::BEARD];

        $data = new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: $specializations,
            rating: 4.5,
            totalAppointments: 100
        );

        $this->assertEquals('Haircut, Beard Trim', $data->getSpecializationsString());
    }

    public function test_get_specializations_string_returns_general_when_empty(): void
    {
        $data = new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: [],
            rating: 4.5,
            totalAppointments: 100
        );

        $this->assertEquals('General', $data->getSpecializationsString());
    }

    public function test_has_specialization_returns_true_when_present(): void
    {
        $specializations = [ServiceType::HAIRCUT, ServiceType::BEARD];

        $data = new BarberData(
            id: 1,
            userId: 10,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
            specializations: $specializations,
            rating: 4.5,
            totalAppointments: 100
        );

        $this->assertTrue($data->hasSpecialization(ServiceType::HAIRCUT));
        $this->assertTrue($data->hasSpecialization(ServiceType::BEARD));
        $this->assertFalse($data->hasSpecialization(ServiceType::COLORING));
    }
}
