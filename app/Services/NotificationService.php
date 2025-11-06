<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendAppointmentConfirmation(Appointment $appointment): void
    {
        // Send email notification
        Log::info('Appointment confirmation sent', ['appointment_id' => $appointment->id]);

        // Queue WhatsApp notification
        // dispatch(new SendWhatsAppNotification($appointment, 'confirmation'));
    }

    public function sendAppointmentReminder(Appointment $appointment): void
    {
        // Send reminder email
        Log::info('Appointment reminder sent', ['appointment_id' => $appointment->id]);

        // Queue WhatsApp notification
        // dispatch(new SendWhatsAppNotification($appointment, 'reminder'));
    }

    public function sendCancellationNotification(Appointment $appointment): void
    {
        // Send cancellation email
        Log::info('Cancellation notification sent', ['appointment_id' => $appointment->id]);
    }
}
