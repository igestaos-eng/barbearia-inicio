@extends('admin.layouts.app')

@section('title', 'Create Barber')
@section('page-title', 'Create New Barber')

@section('content')
<div class="max-w-3xl">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.barbers.index') }}" class="text-gray-400 hover:text-white transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Barbers
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('admin.barbers.store') }}">
            @csrf

            <!-- User Selection -->
            <div class="mb-6">
                <label for="user_id" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-user mr-2"></i>Select User *
                </label>
                <select 
                    id="user_id" 
                    name="user_id" 
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                >
                    <option value="">Choose a user...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-2">
                    <i class="fas fa-info-circle mr-1"></i>Only users with barber role who don't have a profile are shown
                </p>
            </div>

            <!-- Specialization -->
            <div class="mb-6">
                <label for="specialization" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-star mr-2"></i>Specialization
                </label>
                <input 
                    type="text" 
                    id="specialization" 
                    name="specialization" 
                    value="{{ old('specialization') }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="e.g., Classic Cuts, Modern Styles"
                >
                @error('specialization')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio -->
            <div class="mb-6">
                <label for="bio" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-align-left mr-2"></i>Bio
                </label>
                <textarea 
                    id="bio" 
                    name="bio" 
                    rows="4"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="Tell us about this barber..."
                >{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Experience Years -->
            <div class="mb-6">
                <label for="experience_years" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-calendar-alt mr-2"></i>Years of Experience
                </label>
                <input 
                    type="number" 
                    id="experience_years" 
                    name="experience_years" 
                    value="{{ old('experience_years', 0) }}"
                    min="0"
                    max="50"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                >
                @error('experience_years')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo URL -->
            <div class="mb-6">
                <label for="photo" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-image mr-2"></i>Photo URL
                </label>
                <input 
                    type="text" 
                    id="photo" 
                    name="photo" 
                    value="{{ old('photo') }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="https://example.com/photo.jpg"
                >
                @error('photo')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Availability -->
            <div class="mb-6">
                <label class="flex items-center text-gray-300">
                    <input 
                        type="checkbox" 
                        name="is_available" 
                        value="1"
                        {{ old('is_available', true) ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2"
                    >
                    <span class="ml-2">Available for appointments</span>
                </label>
                @error('is_available')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.barbers.index') }}" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Create Barber
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
