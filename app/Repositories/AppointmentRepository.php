<?php

namespace App\Repositories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository
{
    public function __construct(
        private readonly Appointment $model
    ) {
    }

    public function create(array $data): Appointment
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Appointment
    {
        return $this->model->with(['customer.user', 'barber.user', 'service'])->find($id);
    }

    public function update(Appointment $appointment, array $data): bool
    {
        return $appointment->update($data);
    }

    public function delete(Appointment $appointment): bool
    {
        return $appointment->delete();
    }

    public function getUpcomingForBarber(int $barberId): Collection
    {
        return $this->model->forBarber($barberId)
            ->upcoming()
            ->with(['customer.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getUpcomingForCustomer(int $customerId): Collection
    {
        return $this->model->forCustomer($customerId)
            ->upcoming()
            ->with(['barber.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getTodayAppointments(): Collection
    {
        return $this->model->today()
            ->with(['customer.user', 'barber.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getAppointmentsForDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->whereBetween('scheduled_at', [$startDate, $endDate])
            ->with(['customer.user', 'barber.user', 'service'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function hasConflictingAppointment(int $barberId, Carbon $scheduledAt, int $durationMinutes): bool
    {
        $endTime = $scheduledAt->copy()->addMinutes($durationMinutes);

        return $this->model->where('barber_id', $barberId)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED, AppointmentStatus::IN_PROGRESS])
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->whereBetween('scheduled_at', [$scheduledAt, $endTime])
                    ->orWhere(function ($q) use ($scheduledAt, $endTime) {
                        $q->where('scheduled_at', '<=', $scheduledAt)
                            ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$scheduledAt]);
                    });
            })
            ->exists();
    }

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
}
