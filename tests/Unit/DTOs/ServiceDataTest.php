<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ServiceData;
use App\Enums\ServiceType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ServiceDataTest extends TestCase
{
    public function test_can_create_service_data_with_valid_data(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Traditional haircut service',
            price: 50.00,
            duration: 30,
            isActive: true
        );

        $this->assertEquals(1, $data->id);
        $this->assertEquals('Classic Haircut', $data->name);
        $this->assertEquals(ServiceType::HAIRCUT, $data->type);
        $this->assertEquals('Traditional haircut service', $data->description);
        $this->assertEquals(50.00, $data->price);
        $this->assertEquals(30, $data->duration);
        $this->assertTrue($data->isActive);
    }

    public function test_can_create_service_data_without_optional_fields(): void
    {
        $data = new ServiceData(
            id: null,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: null,
            price: 50.00,
            duration: 30
        );

        $this->assertNull($data->id);
        $this->assertNull($data->description);
        $this->assertTrue($data->isActive); // Default value
    }

    public function test_throws_exception_for_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');

        new ServiceData(
            id: 1,
            name: '  ',
            type: ServiceType::HAIRCUT,
            description: 'Test',
            price: 50.00,
            duration: 30
        );
    }

    public function test_throws_exception_for_negative_price(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price cannot be negative');

        new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Test',
            price: -10.00,
            duration: 30
        );
    }

    public function test_throws_exception_for_non_positive_duration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Duration must be positive');

        new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Test',
            price: 50.00,
            duration: 0
        );
    }

    public function test_can_create_from_array(): void
    {
        $data = ServiceData::fromArray([
            'id' => 1,
            'name' => 'Classic Haircut',
            'type' => 'haircut',
            'description' => 'Traditional haircut service',
            'price' => 50.00,
            'duration' => 30,
            'is_active' => true,
        ]);

        $this->assertEquals(1, $data->id);
        $this->assertEquals('Classic Haircut', $data->name);
        $this->assertEquals(ServiceType::HAIRCUT, $data->type);
        $this->assertEquals(50.00, $data->price);
    }

    public function test_can_create_from_array_with_service_type_key(): void
    {
        $data = ServiceData::fromArray([
            'id' => 1,
            'name' => 'Classic Haircut',
            'service_type' => 'haircut',
            'description' => 'Traditional haircut service',
            'price' => 50.00,
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $this->assertEquals(ServiceType::HAIRCUT, $data->type);
        $this->assertEquals(30, $data->duration);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Traditional haircut service',
            price: 50.00,
            duration: 30,
            isActive: true
        );

        $array = $data->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Classic Haircut', $array['name']);
        $this->assertEquals('haircut', $array['service_type']);
        $this->assertEquals('Traditional haircut service', $array['description']);
        $this->assertEquals(50.00, $array['price']);
        $this->assertEquals(30, $array['duration_minutes']);
        $this->assertTrue($array['is_active']);
    }

    public function test_get_formatted_price_returns_correct_string(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Test',
            price: 50.00,
            duration: 30
        );

        $this->assertEquals('R$ 50.00', $data->getFormattedPrice());
        $this->assertEquals('$ 50.00', $data->getFormattedPrice('$'));
    }

    public function test_get_formatted_duration_returns_minutes_correctly(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Quick Service',
            type: ServiceType::BEARD,
            description: 'Test',
            price: 20.00,
            duration: 15
        );

        $this->assertEquals('15 minutes', $data->getFormattedDuration());
    }

    public function test_get_formatted_duration_returns_singular_minute(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Quick Service',
            type: ServiceType::BEARD,
            description: 'Test',
            price: 20.00,
            duration: 1
        );

        $this->assertEquals('1 minute', $data->getFormattedDuration());
    }

    public function test_get_formatted_duration_returns_hours_correctly(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Full Service',
            type: ServiceType::PACKAGE,
            description: 'Test',
            price: 120.00,
            duration: 120
        );

        $this->assertEquals('2 hours', $data->getFormattedDuration());
    }

    public function test_get_formatted_duration_returns_hours_and_minutes_correctly(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Hair Coloring',
            type: ServiceType::COLORING,
            description: 'Test',
            price: 150.00,
            duration: 90
        );

        $this->assertEquals('1 hour 30 minutes', $data->getFormattedDuration());
    }

    public function test_get_type_label_returns_correct_label(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Test',
            price: 50.00,
            duration: 30
        );

        $this->assertEquals('Haircut', $data->getTypeLabel());
    }

    public function test_get_type_icon_returns_correct_icon(): void
    {
        $data = new ServiceData(
            id: 1,
            name: 'Classic Haircut',
            type: ServiceType::HAIRCUT,
            description: 'Test',
            price: 50.00,
            duration: 30
        );

        $this->assertEquals('✂️', $data->getTypeIcon());
    }
}
