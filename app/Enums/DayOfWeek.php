<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';

    public function label(): string
    {
        return match ($this) {
            self::SUNDAY => 'Sunday',
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
        };
    }

    public function short(): string
    {
        return match ($this) {
            self::SUNDAY => 'Sun',
            self::MONDAY => 'Mon',
            self::TUESDAY => 'Tue',
            self::WEDNESDAY => 'Wed',
            self::THURSDAY => 'Thu',
            self::FRIDAY => 'Fri',
            self::SATURDAY => 'Sat',
        };
    }

    public function isWeekend(): bool
    {
        return in_array($this, [self::SUNDAY, self::SATURDAY]);
    }

    public function toNumber(): int
    {
        return match ($this) {
            self::SUNDAY => 1,
            self::MONDAY => 2,
            self::TUESDAY => 3,
            self::WEDNESDAY => 4,
            self::THURSDAY => 5,
            self::FRIDAY => 6,
            self::SATURDAY => 7,
        };
    }

    /**
     * @return array<self>
     */
    public static function weekdays(): array
    {
        return [
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
        ];
    }
}
