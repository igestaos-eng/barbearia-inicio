<?php

namespace App\Services;

use App\DTOs\AppointmentData;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepository $repository,
        private readonly NotificationService $notificationService
    ) {
    }

    public function createAppointment(AppointmentData $data): Appointment
    {
        if ($this->repository->hasConflictingAppointment(
            $data->barberId,
            $data->scheduledAt,
            $data->durationMinutes
        )) {
            throw new \RuntimeException('This time slot is not available');
        }

        return DB::transaction(function () use ($data) {
            $appointment = $this->repository->create($data->toArray());

            $this->notificationService->sendAppointmentConfirmation($appointment);

            return $appointment->load(['customer.user', 'barber.user', 'service']);
        });
    }

    public function confirmAppointment(int $appointmentId): Appointment
    {
        $appointment = $this->repository->findById($appointmentId);

        if (! $appointment) {
            throw new \RuntimeException('Appointment not found');
        }

        if ($appointment->status !== AppointmentStatus::PENDING) {
            throw new \RuntimeException('Only pending appointments can be confirmed');
        }

        $appointment->confirm();

        return $appointment;
    }

    public function cancelAppointment(int $appointmentId, string $reason = null): Appointment
    {
        $appointment = $this->repository->findById($appointmentId);

        if (! $appointment) {
            throw new \RuntimeException('Appointment not found');
        }

        if (! $appointment->status->canBeCancelled()) {
            throw new \RuntimeException('This appointment cannot be cancelled');
        }

        $appointment->cancel($reason);

        $this->notificationService->sendCancellationNotification($appointment);

        return $appointment;
    }

    public function completeAppointment(int $appointmentId): Appointment
    {
        $appointment = $this->repository->findById($appointmentId);

        if (! $appointment) {
            throw new \RuntimeException('Appointment not found');
        }

        if ($appointment->status !== AppointmentStatus::IN_PROGRESS) {
            throw new \RuntimeException('Only in-progress appointments can be completed');
        }

        $appointment->complete();
        $appointment->customer->incrementAppointments();
        $appointment->service->incrementPopularity();

        return $appointment;
    }

    public function sendReminders(): int
    {
        $appointments = $this->repository->getPendingReminders();
        $count = 0;

        foreach ($appointments as $appointment) {
            $this->notificationService->sendAppointmentReminder($appointment);
            $appointment->markReminderSent();
            $count++;
        }

        return $count;
    }
}
