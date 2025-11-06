<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'birth_date',
        'notes',
        'preferences',
        'total_appointments',
        'last_visit_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'total_appointments' => 'integer',
            'last_visit_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function incrementAppointments(): void
    {
        $this->increment('total_appointments');
        $this->last_visit_at = now();
        $this->save();
    }

    public function isNewCustomer(): bool
    {
        return $this->total_appointments === 0;
    }
}
