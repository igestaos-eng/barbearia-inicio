<?php

namespace Tests\Unit\Enums;

use App\Enums\ServiceType;
use PHPUnit\Framework\TestCase;

class ServiceTypeTest extends TestCase
{
    public function test_has_all_required_cases(): void
    {
        $cases = ServiceType::cases();
        $this->assertCount(6, $cases);

        $values = array_map(fn ($case) => $case->value, $cases);
        $this->assertContains('haircut', $values);
        $this->assertContains('beard', $values);
        $this->assertContains('styling', $values);
        $this->assertContains('coloring', $values);
        $this->assertContains('treatment', $values);
        $this->assertContains('package', $values);
    }

    public function test_label_returns_correct_string(): void
    {
        $this->assertEquals('Haircut', ServiceType::HAIRCUT->label());
        $this->assertEquals('Beard Trim', ServiceType::BEARD->label());
        $this->assertEquals('Hair Styling', ServiceType::STYLING->label());
        $this->assertEquals('Hair Coloring', ServiceType::COLORING->label());
        $this->assertEquals('Treatment', ServiceType::TREATMENT->label());
        $this->assertEquals('Package Deal', ServiceType::PACKAGE->label());
    }

    public function test_icon_returns_correct_emoji(): void
    {
        $this->assertEquals('✂️', ServiceType::HAIRCUT->icon());
        $this->assertEquals('🧔', ServiceType::BEARD->icon());
        $this->assertEquals('💇', ServiceType::STYLING->icon());
        $this->assertEquals('🎨', ServiceType::COLORING->icon());
        $this->assertEquals('💆', ServiceType::TREATMENT->icon());
        $this->assertEquals('📦', ServiceType::PACKAGE->icon());
    }

    public function test_default_duration_returns_integer_minutes(): void
    {
        $this->assertIsInt(ServiceType::HAIRCUT->defaultDuration());
        $this->assertEquals(30, ServiceType::HAIRCUT->defaultDuration());
        $this->assertEquals(20, ServiceType::BEARD->defaultDuration());
        $this->assertEquals(45, ServiceType::STYLING->defaultDuration());
        $this->assertEquals(90, ServiceType::COLORING->defaultDuration());
        $this->assertEquals(60, ServiceType::TREATMENT->defaultDuration());
        $this->assertEquals(120, ServiceType::PACKAGE->defaultDuration());
    }

    public function test_all_durations_are_positive(): void
    {
        foreach (ServiceType::cases() as $serviceType) {
            $this->assertGreaterThan(0, $serviceType->defaultDuration());
        }
    }
}
