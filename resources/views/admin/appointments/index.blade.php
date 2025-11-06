@extends('admin.layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointments Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">All Appointments</h2>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-filter mr-2"></i>Filter by Status
                </label>
                <select 
                    id="status" 
                    name="status"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div>
                <label for="date" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-calendar mr-2"></i>Filter by Date
                </label>
                <input 
                    type="date" 
                    id="date" 
                    name="date"
                    value="{{ request('date') }}"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500"
                >
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Scheduled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Barber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                #{{ $appointment->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-white">{{ $appointment->scheduled_at->format('M d, Y') }}</div>
                                <div class="text-gray-400 text-xs">{{ $appointment->scheduled_at->format('H:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-white">{{ $appointment->customer->user->name ?? 'N/A' }}</div>
                                <div class="text-gray-400 text-xs">{{ $appointment->customer->user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $appointment->barber->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $appointment->service->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                {{ $appointment->duration_minutes }} min
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-400 font-semibold">
                                ${{ number_format($appointment->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($appointment->status->value === 'confirmed') bg-green-900 text-green-200
                                    @elseif($appointment->status->value === 'pending') bg-yellow-900 text-yellow-200
                                    @elseif($appointment->status->value === 'completed') bg-blue-900 text-blue-200
                                    @elseif($appointment->status->value === 'cancelled') bg-red-900 text-red-200
                                    @else bg-gray-900 text-gray-200
                                    @endif">
                                    {{ ucfirst($appointment->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('admin.appointments.edit', $appointment) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.appointments.destroy', $appointment) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-400 hover:text-red-300 transition" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-calendar-times text-5xl mb-4 block"></i>
                                <p class="text-lg">No appointments found</p>
                                @if(request()->has('status') || request()->has('date'))
                                    <a href="{{ route('admin.appointments.index') }}" class="text-blue-400 hover:text-blue-300 mt-2 inline-block">
                                        Clear filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($appointments->hasPages())
            <div class="bg-gray-700 px-6 py-4">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
