<?php

namespace Tests\Unit\Enums;

use App\Enums\AppointmentStatus;
use PHPUnit\Framework\TestCase;

class AppointmentStatusTest extends TestCase
{
    public function test_has_all_required_cases(): void
    {
        $cases = AppointmentStatus::cases();
        $this->assertCount(6, $cases);

        $values = array_map(fn ($case) => $case->value, $cases);
        $this->assertContains('pending', $values);
        $this->assertContains('confirmed', $values);
        $this->assertContains('in_progress', $values);
        $this->assertContains('completed', $values);
        $this->assertContains('cancelled', $values);
        $this->assertContains('no_show', $values);
    }

    public function test_label_returns_correct_string(): void
    {
        $this->assertEquals('Pending', AppointmentStatus::PENDING->label());
        $this->assertEquals('Confirmed', AppointmentStatus::CONFIRMED->label());
        $this->assertEquals('In Progress', AppointmentStatus::IN_PROGRESS->label());
        $this->assertEquals('Completed', AppointmentStatus::COMPLETED->label());
        $this->assertEquals('Cancelled', AppointmentStatus::CANCELLED->label());
        $this->assertEquals('No Show', AppointmentStatus::NO_SHOW->label());
    }

    public function test_color_returns_correct_string(): void
    {
        $this->assertEquals('yellow', AppointmentStatus::PENDING->color());
        $this->assertEquals('blue', AppointmentStatus::CONFIRMED->color());
        $this->assertEquals('purple', AppointmentStatus::IN_PROGRESS->color());
        $this->assertEquals('green', AppointmentStatus::COMPLETED->color());
        $this->assertEquals('red', AppointmentStatus::CANCELLED->color());
        $this->assertEquals('gray', AppointmentStatus::NO_SHOW->color());
    }

    public function test_is_editable_returns_true_for_pending_and_confirmed(): void
    {
        $this->assertTrue(AppointmentStatus::PENDING->isEditable());
        $this->assertTrue(AppointmentStatus::CONFIRMED->isEditable());
    }

    public function test_is_editable_returns_false_for_other_statuses(): void
    {
        $this->assertFalse(AppointmentStatus::IN_PROGRESS->isEditable());
        $this->assertFalse(AppointmentStatus::COMPLETED->isEditable());
        $this->assertFalse(AppointmentStatus::CANCELLED->isEditable());
        $this->assertFalse(AppointmentStatus::NO_SHOW->isEditable());
    }

    public function test_is_cancellable_returns_true_for_pending_and_confirmed(): void
    {
        $this->assertTrue(AppointmentStatus::PENDING->isCancellable());
        $this->assertTrue(AppointmentStatus::CONFIRMED->isCancellable());
    }

    public function test_is_cancellable_returns_false_for_other_statuses(): void
    {
        $this->assertFalse(AppointmentStatus::IN_PROGRESS->isCancellable());
        $this->assertFalse(AppointmentStatus::COMPLETED->isCancellable());
        $this->assertFalse(AppointmentStatus::CANCELLED->isCancellable());
        $this->assertFalse(AppointmentStatus::NO_SHOW->isCancellable());
    }
}
