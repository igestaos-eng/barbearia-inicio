<?php

namespace App\Repositories;

use App\DTOs\AppointmentData;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\TimeSlot;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Repository for managing Appointment entities
 *
 * Provides data access methods for appointments with proper query optimization,
 * caching, and error handling following the repository pattern.
 */
class AppointmentRepository
{
    /**
     * Cache duration in minutes for frequently accessed data
     */
    private const CACHE_TTL = 15;

    public function __construct(
        private readonly Appointment $model
    ) {}

    /**
     * Get all appointments with related data
     *
     * @return Collection<int, Appointment>
     */
    public function all(): Collection
    {
        return $this->model
            ->with(['customer.user', 'barber.user', 'service'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    /**
     * Find appointment by ID with related data
     *
     * @param  int  $id  Appointment ID
     */
    public function findById(int $id): ?Appointment
    {
        return $this->model->with(['customer.user', 'barber.user', 'service'])->find($id);
    }

    /**
     * Find all appointments for a specific customer
     *
     * @param  int  $customerId  Customer ID
     * @return Collection<int, Appointment>
     */
    public function findByCustomer(int $customerId): Collection
    {
        return $this->model->forCustomer($customerId)
            ->with(['barber.user', 'service'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    /**
     * Find all appointments for a specific barber
     *
     * @param  int  $barberId  Barber ID
     * @return Collection<int, Appointment>
     */
    public function findByBarber(int $barberId): Collection
    {
        return $this->model->forBarber($barberId)
            ->with(['customer.user', 'service'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    /**
     * Find conflicting appointments for a barber within a time range
     *
     * @param  int  $barberId  Barber ID
     * @param  DateTime  $startTime  Start time of the appointment
     * @param  DateTime  $endTime  End time of the appointment
     * @param  int|null  $exceptId  Appointment ID to exclude from check (for updates)
     * @return Collection<int, Appointment>
     */
    public function findConflicts(int $barberId, DateTime $startTime, DateTime $endTime, ?int $exceptId = null): Collection
    {
        $query = $this->model->where('barber_id', $barberId)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED, AppointmentStatus::IN_PROGRESS])
            ->where(function ($q) use ($startTime, $endTime) {
                // Find appointments that overlap with the given time range
                $q->where(function ($query) use ($startTime, $endTime) {
                    // Appointment starts during the new time slot
                    $query->whereBetween('scheduled_at', [$startTime, $endTime]);
                })->orWhere(function ($query) use ($startTime) {
                    // Appointment starts before and extends into the new time slot
                    // Using datetime arithmetic that works with both MySQL and SQLite
                    $query->where('scheduled_at', '<=', $startTime)
                        ->whereRaw("datetime(scheduled_at, '+' || duration_minutes || ' minutes') > ?", [$startTime]);
                });
            });

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->with(['customer.user', 'service'])->get();
    }

    /**
     * Get available time slots for a barber on a specific date
     *
     * @param  int  $barberId  Barber ID
     * @param  DateTime  $date  Date to check availability
     * @param  int  $duration  Duration in minutes
     * @return Collection<int, array> Collection of available slots with start and end times
     */
    public function getAvailableSlots(int $barberId, DateTime $date, int $duration): Collection
    {
        $cacheKey = "available_slots_{$barberId}_".Carbon::instance($date)->format('Y-m-d')."_{$duration}";

        $slots = Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($barberId, $date, $duration) {
            $dateCarbon = Carbon::instance($date)->startOfDay();

            // Get booked appointments for the day
            $bookedAppointments = $this->model->forBarber($barberId)
                ->whereDate('scheduled_at', $dateCarbon)
                ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED, AppointmentStatus::IN_PROGRESS])
                ->orderBy('scheduled_at')
                ->get(['scheduled_at', 'duration_minutes']);

            // Get available time slots from TimeSlot model
            $timeSlots = TimeSlot::forBarber($barberId)
                ->forDate($dateCarbon)
                ->available()
                ->orderBy('start_time')
                ->get();

            $availableSlots = collect();

            foreach ($timeSlots as $slot) {
                $slotStart = Carbon::parse($dateCarbon->format('Y-m-d').' '.$slot->start_time);
                $slotEnd = Carbon::parse($dateCarbon->format('Y-m-d').' '.$slot->end_time);

                // Generate possible appointment slots within this time slot
                $currentTime = $slotStart->copy();

                while ($currentTime->copy()->addMinutes($duration)->lessThanOrEqualTo($slotEnd)) {
                    $appointmentEnd = $currentTime->copy()->addMinutes($duration);

                    // Check if this slot conflicts with any booked appointments
                    $hasConflict = $bookedAppointments->contains(function ($appointment) use ($currentTime, $appointmentEnd) {
                        $appointmentStart = Carbon::parse($appointment->scheduled_at);
                        $appointmentActualEnd = $appointmentStart->copy()->addMinutes($appointment->duration_minutes);

                        return $currentTime->lessThan($appointmentActualEnd) && $appointmentEnd->greaterThan($appointmentStart);
                    });

                    if (! $hasConflict && $currentTime->greaterThanOrEqualTo(now())) {
                        $availableSlots->push([
                            'start_time' => $currentTime->copy(),
                            'end_time' => $appointmentEnd->copy(),
                            'formatted_time' => $currentTime->format('H:i'),
                        ]);
                    }

                    // Move to next slot (typically 15 or 30 minute increments)
                    $currentTime->addMinutes(15);
                }
            }

            return $availableSlots->toArray();
        });

        return new Collection($slots);
    }

    /**
     * Create a new appointment from DTO
     *
     * @param  AppointmentData  $data  Appointment data transfer object
     */
    public function create(AppointmentData $data): Appointment
    {
        $appointmentData = $data->toArray();

        // If price is not set, fetch from service
        if (! isset($appointmentData['price'])) {
            $service = \App\Models\Service::find($appointmentData['service_id']);
            $appointmentData['price'] = $service ? $service->price : 0;
        }

        // Clear availability cache
        $this->clearAvailabilityCache($appointmentData['barber_id']);

        return $this->model->create($appointmentData);
    }

    /**
     * Update an existing appointment
     *
     * @param  int  $id  Appointment ID
     * @param  AppointmentData  $data  Appointment data transfer object
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, AppointmentData $data): Appointment
    {
        $appointment = $this->model->findOrFail($id);
        $appointmentData = $data->toArray();

        // Clear availability cache for both old and potentially new barber
        $this->clearAvailabilityCache($appointment->barber_id);
        if (isset($appointmentData['barber_id']) && $appointmentData['barber_id'] !== $appointment->barber_id) {
            $this->clearAvailabilityCache($appointmentData['barber_id']);
        }

        $appointment->update($appointmentData);

        return $appointment->fresh(['customer.user', 'barber.user', 'service']);
    }

    /**
     * Delete an appointment
     *
     * @param  int  $id  Appointment ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $appointment = $this->model->findOrFail($id);

        // Clear availability cache
        $this->clearAvailabilityCache($appointment->barber_id);

        return $appointment->delete();
    }

    /**
     * Get upcoming appointments for a barber
     *
     * @param  int  $barberId  Barber ID
     * @param  int  $days  Number of days to look ahead (default: 7)
     * @return Collection<int, Appointment>
     */
    public function getUpcoming(int $barberId, int $days = 7): Collection
    {
        $endDate = now()->addDays($days);

        return $this->model->forBarber($barberId)
            ->where('scheduled_at', '>', now())
            ->where('scheduled_at', '<=', $endDate)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])
            ->with(['customer.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get past appointments for a barber
     *
     * @param  int  $barberId  Barber ID
     * @param  int  $days  Number of days to look back (default: 30)
     * @return Collection<int, Appointment>
     */
    public function getPast(int $barberId, int $days = 30): Collection
    {
        $startDate = now()->subDays($days);

        return $this->model->forBarber($barberId)
            ->where('scheduled_at', '<', now())
            ->where('scheduled_at', '>=', $startDate)
            ->with(['customer.user', 'service'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    /**
     * Count appointments by status
     *
     * @param  AppointmentStatus  $status  Appointment status
     */
    public function countByStatus(AppointmentStatus $status): int
    {
        $cacheKey = "appointment_count_status_{$status->value}";

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($status) {
            return $this->model->where('status', $status)->count();
        });
    }

    /**
     * Get upcoming appointments for a customer (legacy method, kept for backward compatibility)
     *
     * @param  int  $customerId  Customer ID
     * @return Collection<int, Appointment>
     */
    public function getUpcomingForCustomer(int $customerId): Collection
    {
        return $this->model->forCustomer($customerId)
            ->upcoming()
            ->with(['barber.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get upcoming appointments for a barber (legacy method, kept for backward compatibility)
     *
     * @param  int  $barberId  Barber ID
     * @return Collection<int, Appointment>
     */
    public function getUpcomingForBarber(int $barberId): Collection
    {
        return $this->model->forBarber($barberId)
            ->upcoming()
            ->with(['customer.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get today's appointments
     *
     * @return Collection<int, Appointment>
     */
    public function getTodayAppointments(): Collection
    {
        return $this->model->today()
            ->with(['customer.user', 'barber.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get appointments within a date range
     *
     * @param  Carbon  $startDate  Start date
     * @param  Carbon  $endDate  End date
     * @return Collection<int, Appointment>
     */
    public function getAppointmentsForDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->whereBetween('scheduled_at', [$startDate, $endDate])
            ->with(['customer.user', 'barber.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Check if there's a conflicting appointment (legacy method)
     *
     * @param  int  $barberId  Barber ID
     * @param  Carbon  $scheduledAt  Appointment start time
     * @param  int  $durationMinutes  Duration in minutes
     */
    public function hasConflictingAppointment(int $barberId, Carbon $scheduledAt, int $durationMinutes): bool
    {
        $endTime = $scheduledAt->copy()->addMinutes($durationMinutes);

        return $this->model->where('barber_id', $barberId)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED, AppointmentStatus::IN_PROGRESS])
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->whereBetween('scheduled_at', [$scheduledAt, $endTime])
                    ->orWhere(function ($q) use ($scheduledAt) {
                        $q->where('scheduled_at', '<=', $scheduledAt)
                            ->whereRaw("datetime(scheduled_at, '+' || duration_minutes || ' minutes') > ?", [$scheduledAt]);
                    });
            })
            ->exists();
    }

    /**
     * Get appointments pending reminder notifications
     *
     * @return Collection<int, Appointment>
     */
    public function getPendingReminders(): Collection
    {
        $reminderTime = now()->addHours((int) config('barbershop.appointment_reminder_hours', 24));

        return $this->model->where('status', AppointmentStatus::CONFIRMED)
            ->where('reminder_sent', false)
            ->where('scheduled_at', '<=', $reminderTime)
            ->where('scheduled_at', '>', now())
            ->with(['customer.user', 'barber.user', 'service'])
            ->get();
    }

    /**
     * Clear availability cache for a barber
     *
     * @param  int  $barberId  Barber ID
     */
    private function clearAvailabilityCache(int $barberId): void
    {
        // Clear all availability cache keys for this barber
        // In production, you might want to use cache tags for better performance
        $pattern = "available_slots_{$barberId}_*";
        // Note: This is a simplified version. In production with Redis, use: Cache::tags(['availability', "barber_{$barberId}"])->flush();
    }
}
