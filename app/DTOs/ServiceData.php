<?php

namespace App\DTOs;

use App\Enums\ServiceType;
use App\Models\Service;
use InvalidArgumentException;

/**
 * Data Transfer Object for Service
 *
 * Represents service information with proper type safety and validation.
 * Used for transferring service data between layers of the application.
 */
class ServiceData
{
    /**
     * Create a new ServiceData instance
     *
     * @param  int|null  $id  Service ID (null for new services)
     * @param  string  $name  Service name
     * @param  ServiceType  $type  Service type enum
     * @param  string|null  $description  Service description
     * @param  float  $price  Service price
     * @param  int  $duration  Duration in minutes
     * @param  bool  $isActive  Whether the service is active
     *
     * @throws InvalidArgumentException If validation fails
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ServiceType $type,
        public readonly ?string $description,
        public readonly float $price,
        public readonly int $duration,
        public readonly bool $isActive = true,
    ) {
        // Validate name
        if (empty(trim($this->name))) {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        // Validate price is non-negative
        if ($this->price < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }

        // Validate duration is positive
        if ($this->duration <= 0) {
            throw new InvalidArgumentException('Duration must be positive');
        }
    }

    /**
     * Create DTO from array data
     *
     * @param  array  $data  Input data array
     */
    public static function fromArray(array $data): self
    {
        $type = $data['type'] ?? $data['service_type'] ?? null;

        if ($type instanceof ServiceType) {
            $serviceType = $type;
        } elseif (is_string($type)) {
            $serviceType = ServiceType::from($type);
        } else {
            throw new InvalidArgumentException('Service type is required');
        }

        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            type: $serviceType,
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            duration: (int) ($data['duration'] ?? $data['duration_minutes'] ?? 0),
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    /**
     * Create DTO from Eloquent model
     *
     * @param  Service  $service  Service model
     */
    public static function fromModel(Service $service): self
    {
        return new self(
            id: $service->id,
            name: $service->name,
            type: $service->service_type,
            description: $service->description,
            price: (float) $service->price,
            duration: $service->duration_minutes,
            isActive: (bool) $service->is_active,
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'service_type' => $this->type->value,
            'description' => $this->description,
            'price' => $this->price,
            'duration_minutes' => $this->duration,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }

    /**
     * Get formatted price string
     *
     * @param  string  $currency  Currency symbol (default: 'R$' for Brazilian Real)
     * @return string Formatted price (e.g., "R$ 50.00")
     */
    public function getFormattedPrice(string $currency = 'R$'): string
    {
        return sprintf('%s %.2f', $currency, $this->price);
    }

    /**
     * Get formatted duration string
     *
     * @return string Duration in human-readable format (e.g., "30 minutes", "1 hour 30 minutes")
     */
    public function getFormattedDuration(): string
    {
        if ($this->duration < 60) {
            return sprintf('%d minute%s', $this->duration, $this->duration === 1 ? '' : 's');
        }

        $hours = intdiv($this->duration, 60);
        $minutes = $this->duration % 60;

        if ($minutes === 0) {
            return sprintf('%d hour%s', $hours, $hours === 1 ? '' : 's');
        }

        return sprintf('%d hour%s %d minute%s', $hours, $hours === 1 ? '' : 's', $minutes, $minutes === 1 ? '' : 's');
    }

    /**
     * Get service type label
     *
     * @return string Human-readable service type label
     */
    public function getTypeLabel(): string
    {
        return $this->type->label();
    }

    /**
     * Get service type icon
     *
     * @return string Emoji icon for the service type
     */
    public function getTypeIcon(): string
    {
        return $this->type->icon();
    }
}
