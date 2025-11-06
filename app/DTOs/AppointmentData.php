<?php

namespace App\DTOs;

use Carbon\Carbon;

class AppointmentData
{
    public function __construct(
        public readonly int $customerId,
        public readonly int $barberId,
        public readonly int $serviceId,
        public readonly Carbon $scheduledAt,
        public readonly int $durationMinutes,
        public readonly float $price,
        public readonly ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['customer_id'],
            barberId: $data['barber_id'],
            serviceId: $data['service_id'],
            scheduledAt: Carbon::parse($data['scheduled_at']),
            durationMinutes: $data['duration_minutes'],
            price: (float) $data['price'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'barber_id' => $this->barberId,
            'service_id' => $this->serviceId,
            'scheduled_at' => $this->scheduledAt->toDateTimeString(),
            'duration_minutes' => $this->durationMinutes,
            'price' => $this->price,
            'notes' => $this->notes,
            'status' => 'pending',
        ];
    }
}
