<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics.
     */
    public function index(): View
    {
        // Get today's appointments count
        $todayAppointments = Appointment::today()->count();

        // Get total appointments for today with detailed info
        $todayAppointmentsList = Appointment::today()
            ->with(['customer.user', 'barber.user', 'service'])
            ->latest('scheduled_at')
            ->limit(5)
            ->get();

        // Get total barbers
        $totalBarbers = Barber::count();

        // Get active barbers count
        $activeBarbers = Barber::where('is_available', true)->count();

        // Get total services
        $totalServices = Service::count();

        // Get active services count
        $activeServices = Service::where('is_active', true)->count();

        // Get recent bookings (last 10)
        $recentBookings = Appointment::with(['customer.user', 'barber.user', 'service'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Get upcoming appointments count
        $upcomingAppointments = Appointment::upcoming()->count();

        return view('admin.dashboard', compact(
            'todayAppointments',
            'todayAppointmentsList',
            'totalBarbers',
            'activeBarbers',
            'totalServices',
            'activeServices',
            'recentBookings',
            'upcomingAppointments'
        ));
    }
}
