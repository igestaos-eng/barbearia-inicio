<?php

namespace Tests\Unit\Enums;

use App\Enums\DayOfWeek;
use PHPUnit\Framework\TestCase;

class DayOfWeekTest extends TestCase
{
    public function test_has_all_required_cases(): void
    {
        $cases = DayOfWeek::cases();
        $this->assertCount(7, $cases);
    }

    public function test_backing_values_are_integers(): void
    {
        $this->assertEquals(1, DayOfWeek::SUNDAY->value);
        $this->assertEquals(2, DayOfWeek::MONDAY->value);
        $this->assertEquals(3, DayOfWeek::TUESDAY->value);
        $this->assertEquals(4, DayOfWeek::WEDNESDAY->value);
        $this->assertEquals(5, DayOfWeek::THURSDAY->value);
        $this->assertEquals(6, DayOfWeek::FRIDAY->value);
        $this->assertEquals(7, DayOfWeek::SATURDAY->value);
    }

    public function test_label_returns_correct_string(): void
    {
        $this->assertEquals('Sunday', DayOfWeek::SUNDAY->label());
        $this->assertEquals('Monday', DayOfWeek::MONDAY->label());
        $this->assertEquals('Tuesday', DayOfWeek::TUESDAY->label());
        $this->assertEquals('Wednesday', DayOfWeek::WEDNESDAY->label());
        $this->assertEquals('Thursday', DayOfWeek::THURSDAY->label());
        $this->assertEquals('Friday', DayOfWeek::FRIDAY->label());
        $this->assertEquals('Saturday', DayOfWeek::SATURDAY->label());
    }

    public function test_short_returns_three_letter_abbreviation(): void
    {
        $this->assertEquals('Sun', DayOfWeek::SUNDAY->short());
        $this->assertEquals('Mon', DayOfWeek::MONDAY->short());
        $this->assertEquals('Tue', DayOfWeek::TUESDAY->short());
        $this->assertEquals('Wed', DayOfWeek::WEDNESDAY->short());
        $this->assertEquals('Thu', DayOfWeek::THURSDAY->short());
        $this->assertEquals('Fri', DayOfWeek::FRIDAY->short());
        $this->assertEquals('Sat', DayOfWeek::SATURDAY->short());
    }

    public function test_is_weekend_returns_true_for_saturday_and_sunday(): void
    {
        $this->assertTrue(DayOfWeek::SUNDAY->isWeekend());
        $this->assertTrue(DayOfWeek::SATURDAY->isWeekend());
    }

    public function test_is_weekend_returns_false_for_weekdays(): void
    {
        $this->assertFalse(DayOfWeek::MONDAY->isWeekend());
        $this->assertFalse(DayOfWeek::TUESDAY->isWeekend());
        $this->assertFalse(DayOfWeek::WEDNESDAY->isWeekend());
        $this->assertFalse(DayOfWeek::THURSDAY->isWeekend());
        $this->assertFalse(DayOfWeek::FRIDAY->isWeekend());
    }

    public function test_weekdays_returns_array_of_five_days(): void
    {
        $weekdays = DayOfWeek::weekdays();

        $this->assertIsArray($weekdays);
        $this->assertCount(5, $weekdays);
        $this->assertContains(DayOfWeek::MONDAY, $weekdays);
        $this->assertContains(DayOfWeek::TUESDAY, $weekdays);
        $this->assertContains(DayOfWeek::WEDNESDAY, $weekdays);
        $this->assertContains(DayOfWeek::THURSDAY, $weekdays);
        $this->assertContains(DayOfWeek::FRIDAY, $weekdays);
    }

    public function test_weekdays_does_not_include_weekend(): void
    {
        $weekdays = DayOfWeek::weekdays();

        $this->assertNotContains(DayOfWeek::SATURDAY, $weekdays);
        $this->assertNotContains(DayOfWeek::SUNDAY, $weekdays);
    }
}
