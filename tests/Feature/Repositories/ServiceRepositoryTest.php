<?php

namespace Tests\Feature\Repositories;

use App\DTOs\ServiceData;
use App\Enums\ServiceType;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use App\Repositories\ServiceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceRepository(new Service);
    }

    public function test_all_returns_all_services(): void
    {
        Service::factory()->count(3)->create();

        $services = $this->repository->all();

        $this->assertCount(3, $services);
    }

    public function test_find_by_id_returns_service(): void
    {
        $service = Service::factory()->create();

        $found = $this->repository->findById($service->id);

        $this->assertNotNull($found);
        $this->assertEquals($service->id, $found->id);
    }

    public function test_find_active_returns_only_active_services(): void
    {
        Service::factory()->create(['is_active' => true]);
        Service::factory()->create(['is_active' => false]);

        $activeServices = $this->repository->findActive();

        $this->assertCount(1, $activeServices);
        $this->assertTrue($activeServices->first()->is_active);
    }

    public function test_find_by_type_returns_services_of_type(): void
    {
        Service::factory()->create([
            'service_type' => ServiceType::HAIRCUT,
            'is_active' => true,
        ]);

        Service::factory()->create([
            'service_type' => ServiceType::BEARD,
            'is_active' => true,
        ]);

        $haircutServices = $this->repository->findByType(ServiceType::HAIRCUT);

        $this->assertCount(1, $haircutServices);
        $this->assertEquals(ServiceType::HAIRCUT, $haircutServices->first()->service_type);
    }

    public function test_get_by_barber_returns_barber_services(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        $service1 = Service::factory()->create(['is_active' => true]);
        $service2 = Service::factory()->create(['is_active' => true]);
        $service3 = Service::factory()->create(['is_active' => true]);

        $barber->services()->attach([$service1->id, $service2->id]);

        $barberServices = $this->repository->getByBarber($barber->id);

        $this->assertCount(2, $barberServices);
    }

    public function test_create_creates_service_from_dto(): void
    {
        $data = new ServiceData(
            id: null,
            name: 'Test Haircut',
            type: ServiceType::HAIRCUT,
            description: 'A great haircut',
            price: 50.00,
            duration: 30,
            isActive: true
        );

        $service = $this->repository->create($data);

        $this->assertNotNull($service->id);
        $this->assertEquals('Test Haircut', $service->name);
        $this->assertEquals(50.00, (float) $service->price);
        $this->assertEquals(30, $service->duration_minutes);
    }

    public function test_update_updates_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Original Name',
            'price' => 40.00,
        ]);

        $data = new ServiceData(
            id: $service->id,
            name: 'Updated Name',
            type: ServiceType::HAIRCUT,
            description: 'Updated description',
            price: 60.00,
            duration: 45,
            isActive: true
        );

        $updated = $this->repository->update($service->id, $data);

        $this->assertEquals('Updated Name', $updated->name);
        $this->assertEquals(60.00, (float) $updated->price);
        $this->assertEquals(45, $updated->duration_minutes);
    }

    public function test_delete_soft_deletes_service(): void
    {
        $service = Service::factory()->create();

        $result = $this->repository->delete($service->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function test_search_finds_services_by_name(): void
    {
        Service::factory()->create([
            'name' => 'Classic Haircut',
            'is_active' => true,
        ]);

        Service::factory()->create([
            'name' => 'Beard Trim',
            'is_active' => true,
        ]);

        $results = $this->repository->search('Haircut');

        $this->assertCount(1, $results);
        $this->assertStringContainsString('Haircut', $results->first()->name);
    }

    public function test_search_finds_services_by_description(): void
    {
        Service::factory()->create([
            'name' => 'Premium Cut',
            'description' => 'Includes styling and treatment',
            'is_active' => true,
        ]);

        Service::factory()->create([
            'name' => 'Basic Cut',
            'description' => 'Simple haircut',
            'is_active' => true,
        ]);

        $results = $this->repository->search('styling');

        $this->assertCount(1, $results);
    }

    public function test_get_most_popular_returns_top_services(): void
    {
        Service::factory()->create([
            'is_active' => true,
            'popularity' => 100,
        ]);

        Service::factory()->create([
            'is_active' => true,
            'popularity' => 50,
        ]);

        Service::factory()->create([
            'is_active' => true,
            'popularity' => 75,
        ]);

        $popular = $this->repository->getMostPopular(2);

        $this->assertCount(2, $popular);
        $this->assertEquals(100, $popular->first()->popularity);
    }

    public function test_get_price_range_returns_min_and_max(): void
    {
        Service::factory()->create([
            'is_active' => true,
            'price' => 25.00,
        ]);

        Service::factory()->create([
            'is_active' => true,
            'price' => 100.00,
        ]);

        Service::factory()->create([
            'is_active' => true,
            'price' => 50.00,
        ]);

        $priceRange = $this->repository->getPriceRange();

        $this->assertEquals(25.00, $priceRange['min']);
        $this->assertEquals(100.00, $priceRange['max']);
    }
}
