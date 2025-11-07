<?php

namespace App\DTOs;

use App\Enums\ServiceType;
use App\Models\Barber;
use InvalidArgumentException;

/**
 * Data Transfer Object for Barber profile
 *
 * Represents barber information with proper type safety and validation.
 * Used for transferring barber data between layers of the application.
 */
class BarberData
{
    /**
     * Create a new BarberData instance
     *
     * @param  int|null  $id  Barber ID (null for new barbers)
     * @param  int  $userId  User ID
     * @param  string  $name  Barber name
     * @param  string  $email  Barber email
     * @param  string  $phone  Barber phone number
     * @param  array  $specializations  Array of ServiceType enums
     * @param  float  $rating  Barber rating (0.0 to 5.0)
     * @param  int  $totalAppointments  Total number of appointments completed
     * @param  string|null  $bio  Barber biography
     * @param  bool  $isActive  Whether the barber is active
     *
     * @throws InvalidArgumentException If validation fails
     */
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly array $specializations,
        public readonly float $rating,
        public readonly int $totalAppointments,
        public readonly ?string $bio = null,
        public readonly bool $isActive = true,
    ) {
        // Validate user ID
        if ($this->userId <= 0) {
            throw new InvalidArgumentException('User ID must be positive');
        }

        // Validate name
        if (empty(trim($this->name))) {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        // Validate email format
        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        // Validate phone (basic check for non-empty)
        if (empty(trim($this->phone))) {
            throw new InvalidArgumentException('Phone cannot be empty');
        }

        // Validate specializations array contains only ServiceType enums
        if (! empty($this->specializations)) {
            foreach ($this->specializations as $specialization) {
                if (! $specialization instanceof ServiceType) {
                    throw new InvalidArgumentException('Specializations must be ServiceType enum instances');
                }
            }
        }

        // Validate rating range (0.0 to 5.0)
        if ($this->rating < 0.0 || $this->rating > 5.0) {
            throw new InvalidArgumentException('Rating must be between 0.0 and 5.0');
        }

        // Validate total appointments is non-negative
        if ($this->totalAppointments < 0) {
            throw new InvalidArgumentException('Total appointments cannot be negative');
        }
    }

    /**
     * Create DTO from array data
     *
     * @param  array  $data  Input data array
     */
    public static function fromArray(array $data): self
    {
        // Parse specializations
        $specializations = [];
        if (isset($data['specializations'])) {
            foreach ($data['specializations'] as $spec) {
                if ($spec instanceof ServiceType) {
                    $specializations[] = $spec;
                } elseif (is_string($spec)) {
                    $specializations[] = ServiceType::from($spec);
                }
            }
        }

        return new self(
            id: $data['id'] ?? null,
            userId: $data['user_id'],
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            specializations: $specializations,
            rating: (float) ($data['rating'] ?? 0.0),
            totalAppointments: (int) ($data['total_appointments'] ?? 0),
            bio: $data['bio'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    /**
     * Create DTO from Eloquent model
     *
     * @param  Barber  $barber  Barber model
     */
    public static function fromModel(Barber $barber): self
    {
        // Get user data
        $user = $barber->user;

        // Parse specializations from the barber's specialization field or services
        $specializations = [];
        if ($barber->specialization) {
            // If specialization is a string, split by comma and convert to ServiceType enums
            $specs = is_array($barber->specialization)
                ? $barber->specialization
                : explode(',', $barber->specialization);

            foreach ($specs as $spec) {
                try {
                    $trimmedSpec = trim($spec);
                    if (! empty($trimmedSpec)) {
                        $specializations[] = ServiceType::from($trimmedSpec);
                    }
                } catch (\ValueError $e) {
                    // Skip invalid service types
                    continue;
                }
            }
        }

        // If no specializations from field, get from associated services
        if (empty($specializations) && $barber->relationLoaded('services')) {
            foreach ($barber->services as $service) {
                if ($service->service_type && ! in_array($service->service_type, $specializations, true)) {
                    $specializations[] = $service->service_type;
                }
            }
        }

        // Count completed appointments
        $totalAppointments = $barber->relationLoaded('appointments')
            ? $barber->appointments->count()
            : ($barber->total_reviews ?? 0);

        return new self(
            id: $barber->id,
            userId: $barber->user_id,
            name: $user->name ?? 'Unknown',
            email: $user->email ?? '',
            phone: $user->phone ?? '',
            specializations: $specializations,
            rating: (float) ($barber->rating ?? 0.0),
            totalAppointments: $totalAppointments,
            bio: $barber->bio,
            isActive: (bool) ($barber->is_available ?? true),
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'user_id' => $this->userId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'specializations' => array_map(fn (ServiceType $type) => $type->value, $this->specializations),
            'rating' => $this->rating,
            'total_appointments' => $this->totalAppointments,
            'bio' => $this->bio,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }

    /**
     * Get formatted specializations string
     *
     * @return string Comma-separated specialization labels
     */
    public function getSpecializationsString(): string
    {
        if (empty($this->specializations)) {
            return 'General';
        }

        return implode(', ', array_map(fn (ServiceType $type) => $type->label(), $this->specializations));
    }

    /**
     * Check if barber has a specific specialization
     *
     * @param  ServiceType  $type  Service type to check
     */
    public function hasSpecialization(ServiceType $type): bool
    {
        foreach ($this->specializations as $spec) {
            if ($spec === $type) {
                return true;
            }
        }

        return false;
    }
}
