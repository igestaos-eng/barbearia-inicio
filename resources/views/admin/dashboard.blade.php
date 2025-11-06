@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Appointments -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Today's Appointments</p>
                    <h3 class="text-3xl font-bold">{{ $todayAppointments }}</h3>
                </div>
                <div class="bg-blue-500 bg-opacity-50 rounded-full p-4">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Upcoming</p>
                    <h3 class="text-3xl font-bold">{{ $upcomingAppointments }}</h3>
                </div>
                <div class="bg-purple-500 bg-opacity-50 rounded-full p-4">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Barbers -->
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Total Barbers</p>
                    <h3 class="text-3xl font-bold">{{ $totalBarbers }}</h3>
                    <p class="text-green-200 text-xs mt-1">{{ $activeBarbers }} active</p>
                </div>
                <div class="bg-green-500 bg-opacity-50 rounded-full p-4">
                    <i class="fas fa-cut text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Services -->
        <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Total Services</p>
                    <h3 class="text-3xl font-bold">{{ $totalServices }}</h3>
                    <p class="text-orange-200 text-xs mt-1">{{ $activeServices }} active</p>
                </div>
                <div class="bg-orange-500 bg-opacity-50 rounded-full p-4">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Appointments List -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-calendar-check text-blue-500 mr-3"></i>
                Today's Appointments
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Barber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($todayAppointmentsList as $appointment)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $appointment->scheduled_at->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                {{ $appointment->customer->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $appointment->barber->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $appointment->service->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($appointment->status->value === 'confirmed') bg-green-900 text-green-200
                                    @elseif($appointment->status->value === 'pending') bg-yellow-900 text-yellow-200
                                    @elseif($appointment->status->value === 'completed') bg-blue-900 text-blue-200
                                    @else bg-red-900 text-red-200
                                    @endif">
                                    {{ ucfirst($appointment->status->value) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
                                No appointments scheduled for today
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-history text-purple-500 mr-3"></i>
                Recent Bookings
            </h2>
            <a href="{{ route('admin.appointments.index') }}" class="text-blue-400 hover:text-blue-300 text-sm transition">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $booking->scheduled_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                {{ $booking->customer->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $booking->service->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-400">
                                ${{ number_format($booking->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($booking->status->value === 'confirmed') bg-green-900 text-green-200
                                    @elseif($booking->status->value === 'pending') bg-yellow-900 text-yellow-200
                                    @elseif($booking->status->value === 'completed') bg-blue-900 text-blue-200
                                    @else bg-red-900 text-red-200
                                    @endif">
                                    {{ ucfirst($booking->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.appointments.show', $booking) }}" class="text-blue-400 hover:text-blue-300">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                                No recent bookings
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
