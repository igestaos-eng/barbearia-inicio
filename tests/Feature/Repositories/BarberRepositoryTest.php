<?php

namespace Tests\Feature\Repositories;

use App\DTOs\BarberData;
use App\Enums\ServiceType;
use App\Models\Barber;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\WorkingHour;
use App\Repositories\BarberRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarberRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private BarberRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BarberRepository(new Barber);
    }

    public function test_all_returns_all_barbers(): void
    {
        User::factory()->count(3)->create()->each(function ($user) {
            Barber::factory()->create(['user_id' => $user->id]);
        });

        $barbers = $this->repository->all();

        $this->assertCount(3, $barbers);
    }

    public function test_find_by_id_returns_barber(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        $found = $this->repository->findById($barber->id);

        $this->assertNotNull($found);
        $this->assertEquals($barber->id, $found->id);
    }

    public function test_find_active_returns_only_active_barbers(): void
    {
        $activeUser = User::factory()->create();
        Barber::factory()->create(['user_id' => $activeUser->id, 'is_available' => true]);

        $inactiveUser = User::factory()->create();
        Barber::factory()->create(['user_id' => $inactiveUser->id, 'is_available' => false]);

        $activeBarbers = $this->repository->findActive();

        $this->assertCount(1, $activeBarbers);
        $this->assertTrue($activeBarbers->first()->is_available);
    }

    public function test_find_by_service_returns_barbers_offering_service(): void
    {
        $user1 = User::factory()->create();
        $barber1 = Barber::factory()->create(['user_id' => $user1->id]);

        $user2 = User::factory()->create();
        $barber2 = Barber::factory()->create(['user_id' => $user2->id]);

        $service = Service::factory()->create();

        $barber1->services()->attach($service);

        $barbers = $this->repository->findByService($service->id);

        $this->assertCount(1, $barbers);
        $this->assertEquals($barber1->id, $barbers->first()->id);
    }

    public function test_get_with_services_returns_barber_with_services(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        $service1 = Service::factory()->create(['is_active' => true]);
        $service2 = Service::factory()->create(['is_active' => true]);

        $barber->services()->attach([$service1->id, $service2->id]);

        $found = $this->repository->getWithServices($barber->id);

        $this->assertNotNull($found);
        $this->assertCount(2, $found->services);
    }

    public function test_create_creates_barber_from_dto(): void
    {
        $user = User::factory()->create();

        $data = new BarberData(
            id: null,
            userId: $user->id,
            name: $user->name,
            email: $user->email,
            phone: '1234567890',
            specializations: [ServiceType::HAIRCUT, ServiceType::BEARD],
            rating: 4.5,
            totalAppointments: 0,
            bio: 'Test bio',
            isActive: true
        );

        $barber = $this->repository->create($data);

        $this->assertNotNull($barber->id);
        $this->assertEquals($user->id, $barber->user_id);
        $this->assertEquals('Test bio', $barber->bio);
    }

    public function test_update_updates_barber(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Original bio',
        ]);

        $data = new BarberData(
            id: $barber->id,
            userId: $user->id,
            name: $user->name,
            email: $user->email,
            phone: '1234567890',
            specializations: [ServiceType::STYLING],
            rating: 4.8,
            totalAppointments: 10,
            bio: 'Updated bio',
            isActive: true
        );

        $updated = $this->repository->update($barber->id, $data);

        $this->assertEquals('Updated bio', $updated->bio);
        $this->assertEquals(4.8, (float) $updated->rating);
    }

    public function test_delete_soft_deletes_barber(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->delete($barber->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('barbers', ['id' => $barber->id]);
    }

    public function test_get_availability_returns_time_slots(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $date = Carbon::tomorrow();

        TimeSlot::create([
            'barber_id' => $barber->id,
            'date' => $date,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'is_available' => true,
            'is_booked' => false,
        ]);

        $availability = $this->repository->getAvailability($barber->id, $date);

        $this->assertCount(1, $availability);
    }

    public function test_get_working_hours_returns_schedule(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        WorkingHour::create([
            'barber_id' => $barber->id,
            'day_of_week' => \App\Enums\DayOfWeek::MONDAY,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => true,
        ]);

        $workingHours = $this->repository->getWorkingHours($barber->id);

        $this->assertCount(1, $workingHours);
    }

    public function test_get_rating_returns_barber_rating(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create([
            'user_id' => $user->id,
            'rating' => 4.7,
        ]);

        $rating = $this->repository->getRating($barber->id);

        $this->assertEquals(4.7, $rating);
    }

    public function test_get_total_appointments_returns_count(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        $total = $this->repository->getTotalAppointments($barber->id);

        $this->assertEquals(0, $total);
    }

    public function test_search_finds_barbers_by_name(): void
    {
        $user = User::factory()->create(['name' => 'John Barber']);
        Barber::factory()->create(['user_id' => $user->id, 'is_available' => true]);

        $otherUser = User::factory()->create(['name' => 'Jane Stylist']);
        Barber::factory()->create(['user_id' => $otherUser->id, 'is_available' => true]);

        $results = $this->repository->search('John');

        $this->assertCount(1, $results);
        $this->assertEquals('John Barber', $results->first()->user->name);
    }
}
