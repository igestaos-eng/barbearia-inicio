<?php

namespace App\Repositories;

use App\DTOs\BarberData;
use App\Models\Barber;
use App\Models\WorkingHour;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Repository for managing Barber entities
 *
 * Provides data access methods for barbers with proper query optimization,
 * caching, and error handling following the repository pattern.
 */
class BarberRepository
{
    /**
     * Cache duration in minutes for frequently accessed data
     */
    private const CACHE_TTL = 30;

    public function __construct(
        private readonly Barber $model
    ) {}

    /**
     * Get all barbers with related data
     *
     * @return Collection<int, Barber>
     */
    public function all(): Collection
    {
        return $this->model
            ->with(['user', 'services'])
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Find barber by ID
     *
     * @param  int  $id  Barber ID
     */
    public function findById(int $id): ?Barber
    {
        return $this->model->with(['user'])->find($id);
    }

    /**
     * Get all active barbers
     *
     * @return Collection<int, Barber>
     */
    public function findActive(): Collection
    {
        return Cache::remember('barbers_active', self::CACHE_TTL * 60, function () {
            return $this->model
                ->where('is_available', true)
                ->with(['user', 'services'])
                ->orderBy('rating', 'desc')
                ->get();
        });
    }

    /**
     * Find barbers that offer a specific service
     *
     * @param  int  $serviceId  Service ID
     * @return Collection<int, Barber>
     */
    public function findByService(int $serviceId): Collection
    {
        return $this->model
            ->whereHas('services', function ($query) use ($serviceId) {
                $query->where('services.id', $serviceId);
            })
            ->where('is_available', true)
            ->with(['user', 'services'])
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Get barber with all related services
     *
     * @param  int  $id  Barber ID
     */
    public function getWithServices(int $id): ?Barber
    {
        return $this->model
            ->with(['user', 'services' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('price');
            }])
            ->find($id);
    }

    /**
     * Create a new barber from DTO
     *
     * @param  BarberData  $data  Barber data transfer object
     */
    public function create(BarberData $data): Barber
    {
        $barberData = [
            'user_id' => $data->userId,
            'specialization' => implode(',', array_map(fn ($type) => $type->value, $data->specializations)),
            'bio' => $data->bio,
            'rating' => $data->rating,
            'is_available' => $data->isActive,
        ];

        // Clear active barbers cache
        Cache::forget('barbers_active');

        return $this->model->create($barberData);
    }

    /**
     * Update an existing barber
     *
     * @param  int  $id  Barber ID
     * @param  BarberData  $data  Barber data transfer object
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, BarberData $data): Barber
    {
        $barber = $this->model->findOrFail($id);

        $barberData = [
            'specialization' => implode(',', array_map(fn ($type) => $type->value, $data->specializations)),
            'bio' => $data->bio,
            'rating' => $data->rating,
            'is_available' => $data->isActive,
        ];

        $barber->update($barberData);

        // Clear relevant caches
        Cache::forget('barbers_active');
        Cache::forget("barber_rating_{$id}");

        return $barber->fresh(['user', 'services']);
    }

    /**
     * Delete a barber (soft delete)
     *
     * @param  int  $id  Barber ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $barber = $this->model->findOrFail($id);

        // Clear relevant caches
        Cache::forget('barbers_active');
        Cache::forget("barber_rating_{$id}");

        return $barber->delete();
    }

    /**
     * Get barber's availability for a specific date
     *
     * Returns time slots or working hours for the given date
     *
     * @param  int  $barberId  Barber ID
     * @param  DateTime  $date  Date to check availability
     * @return Collection<int, mixed> Collection of time slots or working hours
     */
    public function getAvailability(int $barberId, DateTime $date): Collection
    {
        $barber = $this->model->find($barberId);

        if (! $barber) {
            return collect();
        }

        // Get time slots for the specific date
        return $barber->timeSlots()
            ->forDate($date)
            ->available()
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get barber's working hours schedule
     *
     * @param  int  $barberId  Barber ID
     * @return Collection<int, WorkingHour>
     */
    public function getWorkingHours(int $barberId): Collection
    {
        $workingHours = WorkingHour::where('barber_id', $barberId)
            ->workingDays()
            ->get();

        // Sort by day of week order (works with both MySQL and SQLite)
        return $workingHours->sort(function ($a, $b) {
            $order = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7];

            return ($order[$a->day_of_week->value] ?? 99) <=> ($order[$b->day_of_week->value] ?? 99);
        })->values();
    }

    /**
     * Get barber's average rating
     *
     * @param  int  $barberId  Barber ID
     * @return float Average rating (0.0 to 5.0)
     */
    public function getRating(int $barberId): float
    {
        $cacheKey = "barber_rating_{$barberId}";

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($barberId) {
            $barber = $this->model->find($barberId);

            return $barber ? (float) $barber->rating : 0.0;
        });
    }

    /**
     * Get total number of appointments completed by a barber
     *
     * @param  int  $barberId  Barber ID
     * @return int Total appointments count
     */
    public function getTotalAppointments(int $barberId): int
    {
        $cacheKey = "barber_appointments_total_{$barberId}";

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($barberId) {
            return $this->model
                ->withCount('appointments')
                ->find($barberId)
                ?->appointments_count ?? 0;
        });
    }

    /**
     * Search barbers by name or specialization
     *
     * @param  string  $query  Search query
     * @return Collection<int, Barber>
     */
    public function search(string $query): Collection
    {
        $searchTerm = '%'.strtolower($query).'%';

        return $this->model
            ->whereHas('user', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
            })
            ->orWhereRaw('LOWER(specialization) LIKE ?', [$searchTerm])
            ->orWhereRaw('LOWER(bio) LIKE ?', [$searchTerm])
            ->where('is_available', true)
            ->with(['user', 'services'])
            ->orderBy('rating', 'desc')
            ->get();
    }
}
