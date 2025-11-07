<?php

namespace Tests\Feature\Repositories;

use App\DTOs\AppointmentData;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Customer;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AppointmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentRepository(new Appointment);
    }

    public function test_all_returns_all_appointments(): void
    {
        // Create test data
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        Appointment::factory()->count(3)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        $appointments = $this->repository->all();

        $this->assertCount(3, $appointments);
    }

    public function test_find_by_id_returns_appointment(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        $found = $this->repository->findById($appointment->id);

        $this->assertNotNull($found);
        $this->assertEquals($appointment->id, $found->id);
    }

    public function test_find_by_customer_returns_customer_appointments(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        Appointment::factory()->count(2)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        // Create appointment for different customer
        $otherCustomer = Customer::factory()->create(['user_id' => User::factory()->create()->id]);
        Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $otherCustomer->id,
            'service_id' => $service->id,
        ]);

        $appointments = $this->repository->findByCustomer($customer->id);

        $this->assertCount(2, $appointments);
        $this->assertTrue($appointments->every(fn ($apt) => $apt->customer_id === $customer->id));
    }

    public function test_find_by_barber_returns_barber_appointments(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        Appointment::factory()->count(3)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        // Create appointment for different barber
        $otherBarber = Barber::factory()->create(['user_id' => User::factory()->create()->id]);
        Appointment::factory()->create([
            'barber_id' => $otherBarber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        $appointments = $this->repository->findByBarber($barber->id);

        $this->assertCount(3, $appointments);
        $this->assertTrue($appointments->every(fn ($apt) => $apt->barber_id === $barber->id));
    }

    public function test_find_conflicts_detects_overlapping_appointments(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        // Create an existing appointment from 10:00 to 11:00
        $existingTime = Carbon::now()->addDay()->setTime(10, 0);
        Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'scheduled_at' => $existingTime,
            'duration_minutes' => 60,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        // Check for conflict with 10:30 to 11:30
        $startTime = $existingTime->copy()->addMinutes(30);
        $endTime = $startTime->copy()->addMinutes(60);

        $conflicts = $this->repository->findConflicts($barber->id, $startTime, $endTime);

        $this->assertCount(1, $conflicts);
    }

    public function test_get_available_slots_returns_available_times(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $date = Carbon::tomorrow();

        // Create time slots for the barber
        TimeSlot::create([
            'barber_id' => $barber->id,
            'date' => $date,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'is_available' => true,
            'is_booked' => false,
        ]);

        $availableSlots = $this->repository->getAvailableSlots($barber->id, $date, 30);

        $this->assertGreaterThan(0, $availableSlots->count());
    }

    public function test_create_creates_appointment_from_dto(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        $startTime = Carbon::now()->addDay()->setTime(10, 0);
        $endTime = $startTime->copy()->addMinutes(30);

        $data = new AppointmentData(
            id: null,
            customerId: $customer->id,
            barberId: $barber->id,
            serviceId: $service->id,
            appointmentDate: $startTime,
            startTime: $startTime,
            endTime: $endTime,
            status: AppointmentStatus::PENDING,
            notes: 'Test appointment'
        );

        $appointment = $this->repository->create($data);

        $this->assertNotNull($appointment->id);
        $this->assertEquals($customer->id, $appointment->customer_id);
        $this->assertEquals($barber->id, $appointment->barber_id);
        $this->assertEquals($service->id, $appointment->service_id);
    }

    public function test_update_updates_appointment(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'notes' => 'Original notes',
        ]);

        $startTime = Carbon::parse($appointment->scheduled_at);
        $endTime = $startTime->copy()->addMinutes($appointment->duration_minutes);

        $data = new AppointmentData(
            id: $appointment->id,
            customerId: $customer->id,
            barberId: $barber->id,
            serviceId: $service->id,
            appointmentDate: $startTime,
            startTime: $startTime,
            endTime: $endTime,
            status: AppointmentStatus::CONFIRMED,
            notes: 'Updated notes'
        );

        $updated = $this->repository->update($appointment->id, $data);

        $this->assertEquals('Updated notes', $updated->notes);
        $this->assertEquals(AppointmentStatus::CONFIRMED, $updated->status);
    }

    public function test_delete_removes_appointment(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        $result = $this->repository->delete($appointment->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    public function test_get_upcoming_returns_future_appointments(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        // Create past appointment
        Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'scheduled_at' => Carbon::now()->subDay(),
            'status' => AppointmentStatus::COMPLETED,
        ]);

        // Create upcoming appointments
        Appointment::factory()->count(2)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'scheduled_at' => Carbon::now()->addDays(2),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $upcoming = $this->repository->getUpcoming($barber->id, 7);

        $this->assertCount(2, $upcoming);
        $this->assertTrue($upcoming->every(fn ($apt) => $apt->scheduled_at > now()));
    }

    public function test_get_past_returns_previous_appointments(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        // Create past appointments
        Appointment::factory()->count(2)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'scheduled_at' => Carbon::now()->subDays(5),
        ]);

        // Create future appointment
        Appointment::factory()->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'scheduled_at' => Carbon::now()->addDay(),
            'status' => AppointmentStatus::PENDING,
        ]);

        $past = $this->repository->getPast($barber->id, 30);

        $this->assertCount(2, $past);
        $this->assertTrue($past->every(fn ($apt) => $apt->scheduled_at < now()));
    }

    public function test_count_by_status_returns_correct_count(): void
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create(['user_id' => $user->id]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        Appointment::factory()->count(3)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'status' => AppointmentStatus::PENDING,
        ]);

        Appointment::factory()->count(2)->create([
            'barber_id' => $barber->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        $pendingCount = $this->repository->countByStatus(AppointmentStatus::PENDING);
        $confirmedCount = $this->repository->countByStatus(AppointmentStatus::CONFIRMED);

        $this->assertEquals(3, $pendingCount);
        $this->assertEquals(2, $confirmedCount);
    }
}
