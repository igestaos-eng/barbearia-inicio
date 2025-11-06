<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'is_booked',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_available' => 'boolean',
            'is_booked' => 'boolean',
        ];
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public function book(): void
    {
        $this->is_booked = true;
        $this->is_available = false;
        $this->save();
    }

    public function release(): void
    {
        $this->is_booked = false;
        $this->is_available = true;
        $this->save();
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('is_booked', false);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForBarber($query, int $barberId)
    {
        return $query->where('barber_id', $barberId);
    }
}
