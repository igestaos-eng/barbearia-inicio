<?php

namespace App\DTOs;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use DateTime;
use InvalidArgumentException;

/**
 * Data Transfer Object for Appointment data
 *
 * Represents appointment information with proper type safety and validation.
 * Used for transferring appointment data between layers of the application.
 */
class AppointmentData
{
    /**
     * Create a new AppointmentData instance
     *
     * @param  int|null  $id  Appointment ID (null for new appointments)
     * @param  int  $customerId  Customer ID
     * @param  int  $barberId  Barber ID
     * @param  int  $serviceId  Service ID
     * @param  Carbon|DateTime  $appointmentDate  Date of the appointment
     * @param  Carbon|DateTime  $startTime  Start time of the appointment
     * @param  Carbon|DateTime  $endTime  End time of the appointment
     * @param  AppointmentStatus  $status  Appointment status
     * @param  string|null  $notes  Additional notes
     * @param  Carbon|null  $createdAt  Creation timestamp
     * @param  Carbon|null  $updatedAt  Last update timestamp
     *
     * @throws InvalidArgumentException If validation fails
     */
    public function __construct(
        public readonly ?int $id,
        public readonly int $customerId,
        public readonly int $barberId,
        public readonly int $serviceId,
        public readonly Carbon|DateTime $appointmentDate,
        public readonly Carbon|DateTime $startTime,
        public readonly Carbon|DateTime $endTime,
        public readonly AppointmentStatus $status,
        public readonly ?string $notes = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
    ) {
        // Validate IDs are positive
        if ($this->customerId <= 0) {
            throw new InvalidArgumentException('Customer ID must be positive');
        }
        if ($this->barberId <= 0) {
            throw new InvalidArgumentException('Barber ID must be positive');
        }
        if ($this->serviceId <= 0) {
            throw new InvalidArgumentException('Service ID must be positive');
        }

        // Validate end time is after start time
        $start = $startTime instanceof Carbon ? $startTime : Carbon::instance($startTime);
        $end = $endTime instanceof Carbon ? $endTime : Carbon::instance($endTime);

        if ($end->lte($start)) {
            throw new InvalidArgumentException('End time must be after start time');
        }
    }

    /**
     * Create DTO from array data
     *
     * @param  array  $data  Input data array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            customerId: $data['customer_id'],
            barberId: $data['barber_id'],
            serviceId: $data['service_id'],
            appointmentDate: Carbon::parse($data['appointment_date'] ?? $data['scheduled_at']),
            startTime: Carbon::parse($data['start_time'] ?? $data['scheduled_at']),
            endTime: Carbon::parse($data['end_time'] ?? $data['scheduled_at'])->addMinutes($data['duration_minutes'] ?? 0),
            status: $data['status'] instanceof AppointmentStatus ? $data['status'] : AppointmentStatus::from($data['status'] ?? 'pending'),
            notes: $data['notes'] ?? null,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    /**
     * Create DTO from Eloquent model
     *
     * @param  Appointment  $appointment  Appointment model
     */
    public static function fromModel(Appointment $appointment): self
    {
        $scheduledAt = $appointment->scheduled_at instanceof Carbon
            ? $appointment->scheduled_at
            : Carbon::parse($appointment->scheduled_at);

        $endTime = $scheduledAt->copy()->addMinutes($appointment->duration_minutes);

        return new self(
            id: $appointment->id,
            customerId: $appointment->customer_id,
            barberId: $appointment->barber_id,
            serviceId: $appointment->service_id,
            appointmentDate: $scheduledAt,
            startTime: $scheduledAt,
            endTime: $endTime,
            status: $appointment->status,
            notes: $appointment->notes,
            createdAt: $appointment->created_at,
            updatedAt: $appointment->updated_at,
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $startTime = $this->startTime instanceof Carbon
            ? $this->startTime
            : Carbon::instance($this->startTime);

        $endTime = $this->endTime instanceof Carbon
            ? $this->endTime
            : Carbon::instance($this->endTime);

        return array_filter([
            'id' => $this->id,
            'customer_id' => $this->customerId,
            'barber_id' => $this->barberId,
            'service_id' => $this->serviceId,
            'scheduled_at' => $startTime->toDateTimeString(),
            'duration_minutes' => $startTime->diffInMinutes($endTime),
            'status' => $this->status->value,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->toDateTimeString(),
            'updated_at' => $this->updatedAt?->toDateTimeString(),
        ], fn ($value) => $value !== null);
    }
}
