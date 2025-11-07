<?php

namespace App\Repositories;

use App\DTOs\ServiceData;
use App\Enums\ServiceType;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Repository for managing Service entities
 *
 * Provides data access methods for services with proper query optimization,
 * caching, and error handling following the repository pattern.
 */
class ServiceRepository
{
    /**
     * Cache duration in minutes for frequently accessed data
     */
    private const CACHE_TTL = 60;

    public function __construct(
        private readonly Service $model
    ) {}

    /**
     * Get all services
     *
     * @return Collection<int, Service>
     */
    public function all(): Collection
    {
        return $this->model
            ->orderBy('popularity', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find service by ID
     *
     * @param  int  $id  Service ID
     */
    public function findById(int $id): ?Service
    {
        return $this->model->find($id);
    }

    /**
     * Get all active services
     *
     * @return Collection<int, Service>
     */
    public function findActive(): Collection
    {
        return Cache::remember('services_active', self::CACHE_TTL * 60, function () {
            return $this->model
                ->active()
                ->orderBy('popularity', 'desc')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Find services by type
     *
     * @param  ServiceType  $type  Service type enum
     * @return Collection<int, Service>
     */
    public function findByType(ServiceType $type): Collection
    {
        $cacheKey = "services_type_{$type->value}";

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($type) {
            return $this->model
                ->where('service_type', $type)
                ->where('is_active', true)
                ->orderBy('popularity', 'desc')
                ->orderBy('price')
                ->get();
        });
    }

    /**
     * Get services offered by a specific barber
     *
     * @param  int  $barberId  Barber ID
     * @return Collection<int, Service>
     */
    public function getByBarber(int $barberId): Collection
    {
        return $this->model
            ->whereHas('barbers', function ($query) use ($barberId) {
                $query->where('barbers.id', $barberId);
            })
            ->where('is_active', true)
            ->orderBy('price')
            ->get();
    }

    /**
     * Create a new service from DTO
     *
     * @param  ServiceData  $data  Service data transfer object
     */
    public function create(ServiceData $data): Service
    {
        $serviceData = [
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'duration_minutes' => $data->duration,
            'service_type' => $data->type,
            'is_active' => $data->isActive,
            'popularity' => 0,
        ];

        // Clear relevant caches
        Cache::forget('services_active');
        Cache::forget("services_type_{$data->type->value}");

        return $this->model->create($serviceData);
    }

    /**
     * Update an existing service
     *
     * @param  int  $id  Service ID
     * @param  ServiceData  $data  Service data transfer object
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, ServiceData $data): Service
    {
        $service = $this->model->findOrFail($id);

        $oldType = $service->service_type;

        $serviceData = [
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'duration_minutes' => $data->duration,
            'service_type' => $data->type,
            'is_active' => $data->isActive,
        ];

        $service->update($serviceData);

        // Clear relevant caches
        Cache::forget('services_active');
        Cache::forget("services_type_{$data->type->value}");
        if ($oldType !== $data->type) {
            Cache::forget("services_type_{$oldType->value}");
        }
        Cache::forget('services_most_popular');
        Cache::forget('services_price_range');

        return $service->fresh();
    }

    /**
     * Delete a service (soft delete)
     *
     * @param  int  $id  Service ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $service = $this->model->findOrFail($id);

        // Clear relevant caches
        Cache::forget('services_active');
        Cache::forget("services_type_{$service->service_type->value}");
        Cache::forget('services_most_popular');
        Cache::forget('services_price_range');

        return $service->delete();
    }

    /**
     * Search services by name or description
     *
     * @param  string  $query  Search query
     * @return Collection<int, Service>
     */
    public function search(string $query): Collection
    {
        $searchTerm = '%'.strtolower($query).'%';

        return $this->model
            ->where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
            })
            ->orderBy('popularity', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get most popular services
     *
     * @param  int  $limit  Maximum number of services to return (default: 5)
     * @return Collection<int, Service>
     */
    public function getMostPopular(int $limit = 5): Collection
    {
        $cacheKey = "services_most_popular_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL * 60, function () use ($limit) {
            return $this->model
                ->active()
                ->popular()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get price range for all active services
     *
     * Returns an array with 'min' and 'max' keys
     *
     * @return array{min: float, max: float}
     */
    public function getPriceRange(): array
    {
        return Cache::remember('services_price_range', self::CACHE_TTL * 60, function () {
            $result = $this->model
                ->active()
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();

            return [
                'min' => $result->min_price ? (float) $result->min_price : 0.0,
                'max' => $result->max_price ? (float) $result->max_price : 0.0,
            ];
        });
    }
}
