<?php

namespace App\DTOs;

use Carbon\Carbon;
use DateTime;
use InvalidArgumentException;

/**
 * Data Transfer Object for Availability/Time Slots
 *
 * Represents barber availability information with time slots.
 * Used for managing and transferring availability data.
 */
class AvailabilityData
{
    /**
     * Create a new AvailabilityData instance
     *
     * @param  int  $barberId  Barber ID
     * @param  Carbon|DateTime  $date  Date for availability
     * @param  string  $startTime  Start time in H:i format (e.g., "09:00")
     * @param  string  $endTime  End time in H:i format (e.g., "17:00")
     * @param  bool  $isAvailable  Whether the slot is available
     * @param  int  $duration  Duration in minutes
     *
     * @throws InvalidArgumentException If validation fails
     */
    public function __construct(
        public readonly int $barberId,
        public readonly Carbon|DateTime $date,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly bool $isAvailable,
        public readonly int $duration,
    ) {
        // Validate barber ID
        if ($this->barberId <= 0) {
            throw new InvalidArgumentException('Barber ID must be positive');
        }

        // Validate time format (H:i)
        if (! preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $this->startTime)) {
            throw new InvalidArgumentException('Start time must be in H:i format (e.g., "9:00" or "09:00")');
        }
        if (! preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $this->endTime)) {
            throw new InvalidArgumentException('End time must be in H:i format (e.g., "17:00" or "5:00")');
        }

        // Validate duration is positive
        if ($this->duration <= 0) {
            throw new InvalidArgumentException('Duration must be positive');
        }

        // Validate end time is after start time
        $start = Carbon::createFromFormat('H:i', $this->startTime);
        $end = Carbon::createFromFormat('H:i', $this->endTime);
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
            barberId: $data['barber_id'],
            date: Carbon::parse($data['date']),
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            isAvailable: $data['is_available'] ?? true,
            duration: $data['duration'] ?? 30,
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $date = $this->date instanceof Carbon
            ? $this->date
            : Carbon::instance($this->date);

        return [
            'barber_id' => $this->barberId,
            'date' => $date->toDateString(),
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'is_available' => $this->isAvailable,
            'duration' => $this->duration,
        ];
    }

    /**
     * Check if the slot is available
     */
    public function hasAvailability(): bool
    {
        return $this->isAvailable;
    }

    /**
     * Calculate total available minutes
     *
     * @return int Total minutes in the slot
     */
    public function getTotalMinutes(): int
    {
        $start = Carbon::createFromFormat('H:i', $this->startTime);
        $end = Carbon::createFromFormat('H:i', $this->endTime);

        return $start->diffInMinutes($end);
    }

    /**
     * Calculate how many appointments can fit in this slot
     *
     * @return int Number of possible appointments
     */
    public function getPossibleAppointments(): int
    {
        if (! $this->isAvailable) {
            return 0;
        }

        return (int) floor($this->getTotalMinutes() / $this->duration);
    }

    /**
     * Get formatted time range string
     *
     * @return string Formatted time range (e.g., "09:00 - 17:00")
     */
    public function getTimeRangeString(): string
    {
        return sprintf('%s - %s', $this->startTime, $this->endTime);
    }
}
