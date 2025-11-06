<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'photo',
        'experience_years',
        'rating',
        'total_reviews',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'rating' => 'decimal:2',
            'total_reviews' => 'integer',
            'is_available' => 'boolean',
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

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'services_barbers')
            ->withTimestamps();
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function updateRating(float $newRating): void
    {
        $this->total_reviews++;
        $this->rating = (($this->rating * ($this->total_reviews - 1)) + $newRating) / $this->total_reviews;
        $this->save();
    }
}
