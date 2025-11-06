@extends('admin.layouts.app')

@section('title', 'Barbers')
@section('page-title', 'Barbers Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">All Barbers</h2>
        <a href="{{ route('admin.barbers.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add New Barber
        </a>
    </div>

    <!-- Barbers Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Specialization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Experience</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($barbers as $barber)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                #{{ $barber->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-gray-300"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $barber->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $barber->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $barber->specialization ?? 'General' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $barber->experience_years ?? 0 }} years
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-white">{{ number_format($barber->rating ?? 0, 1) }}</span>
                                    <span class="text-gray-400 text-xs ml-1">({{ $barber->total_reviews ?? 0 }})</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $barber->is_available ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                                    {{ $barber->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('admin.barbers.edit', $barber) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.barbers.destroy', $barber) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this barber?');">
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-users-slash text-5xl mb-4 block"></i>
                                <p class="text-lg">No barbers found</p>
                                <a href="{{ route('admin.barbers.create') }}" class="text-blue-400 hover:text-blue-300 mt-2 inline-block">
                                    Add your first barber
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($barbers->hasPages())
            <div class="bg-gray-700 px-6 py-4">
                {{ $barbers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
