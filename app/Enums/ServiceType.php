<?php

namespace App\Enums;

enum ServiceType: string
{
    case HAIRCUT = 'haircut';
    case BEARD = 'beard';
    case STYLING = 'styling';
    case COLORING = 'coloring';
    case TREATMENT = 'treatment';
    case PACKAGE = 'package';

    public function label(): string
    {
        return match ($this) {
            self::HAIRCUT => 'Haircut',
            self::BEARD => 'Beard Trim',
            self::STYLING => 'Hair Styling',
            self::COLORING => 'Hair Coloring',
            self::TREATMENT => 'Treatment',
            self::PACKAGE => 'Package Deal',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::HAIRCUT => '✂️',
            self::BEARD => '🧔',
            self::STYLING => '💇',
            self::COLORING => '🎨',
            self::TREATMENT => '💆',
            self::PACKAGE => '📦',
        };
    }

    public function defaultDuration(): int
    {
        return match ($this) {
            self::HAIRCUT => 30,
            self::BEARD => 20,
            self::STYLING => 45,
            self::COLORING => 90,
            self::TREATMENT => 60,
            self::PACKAGE => 120,
        };
    }
}
