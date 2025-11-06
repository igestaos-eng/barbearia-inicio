<?php

namespace App\DTOs;

use Carbon\Carbon;

class AvailabilityData
{
    public function __construct(
        public readonly int $barberId,
        public readonly Carbon $date,
        public readonly array $availableSlots,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            barberId: $data['barber_id'],
            date: Carbon::parse($data['date']),
            availableSlots: $data['available_slots'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'barber_id' => $this->barberId,
            'date' => $this->date->toDateString(),
            'available_slots' => $this->availableSlots,
        ];
    }

    public function hasAvailableSlots(): bool
    {
        return count($this->availableSlots) > 0;
    }
}
