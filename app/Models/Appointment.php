<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'barber_id',
        'service_id',
        'scheduled_at',
        'completed_at',
        'duration_minutes',
        'status',
        'notes',
        'cancellation_reason',
        'price',
        'reminder_sent',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'duration_minutes' => 'integer',
            'status' => AppointmentStatus::class,
            'price' => 'decimal:2',
            'reminder_sent' => 'boolean',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function confirm(): void
    {
        $this->status = AppointmentStatus::CONFIRMED;
        $this->save();
    }

    public function cancel(string $reason = null): void
    {
        $this->status = AppointmentStatus::CANCELLED;
        $this->cancellation_reason = $reason;
        $this->save();
    }

    public function complete(): void
    {
        $this->status = AppointmentStatus::COMPLETED;
        $this->completed_at = now();
        $this->save();
    }

    public function markReminderSent(): void
    {
        $this->reminder_sent = true;
        $this->reminder_sent_at = now();
        $this->save();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeForBarber($query, int $barberId)
    {
        return $query->where('barber_id', $barberId);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
