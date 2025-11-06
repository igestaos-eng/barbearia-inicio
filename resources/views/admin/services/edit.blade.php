@extends('admin.layouts.app')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('content')
<div class="max-w-3xl">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.services.index') }}" class="text-gray-400 hover:text-white transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Services
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('admin.services.update', $service) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-tag mr-2"></i>Service Name *
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $service->name) }}"
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="e.g., Classic Haircut"
                >
                @error('name')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-align-left mr-2"></i>Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="Describe this service..."
                >{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Service Type -->
            <div class="mb-6">
                <label for="service_type" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-th-large mr-2"></i>Service Type *
                </label>
                <select 
                    id="service_type" 
                    name="service_type" 
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                >
                    <option value="">Select type...</option>
                    <option value="haircut" {{ old('service_type', $service->service_type->value) == 'haircut' ? 'selected' : '' }}>Haircut</option>
                    <option value="beard" {{ old('service_type', $service->service_type->value) == 'beard' ? 'selected' : '' }}>Beard</option>
                    <option value="hair_and_beard" {{ old('service_type', $service->service_type->value) == 'hair_and_beard' ? 'selected' : '' }}>Hair & Beard</option>
                    <option value="kids" {{ old('service_type', $service->service_type->value) == 'kids' ? 'selected' : '' }}>Kids</option>
                    <option value="spa" {{ old('service_type', $service->service_type->value) == 'spa' ? 'selected' : '' }}>Spa</option>
                    <option value="other" {{ old('service_type', $service->service_type->value) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('service_type')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price and Duration Row -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Price -->
                <div>
                    <label for="price" class="block text-gray-300 text-sm font-medium mb-2">
                        <i class="fas fa-dollar-sign mr-2"></i>Price *
                    </label>
                    <input 
                        type="number" 
                        id="price" 
                        name="price" 
                        value="{{ old('price', $service->price) }}"
                        step="0.01"
                        min="0"
                        max="9999.99"
                        required
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="0.00"
                    >
                    @error('price')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-gray-300 text-sm font-medium mb-2">
                        <i class="fas fa-clock mr-2"></i>Duration (min) *
                    </label>
                    <input 
                        type="number" 
                        id="duration_minutes" 
                        name="duration_minutes" 
                        value="{{ old('duration_minutes', $service->duration_minutes) }}"
                        min="5"
                        max="480"
                        required
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                    @error('duration_minutes')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Image URL -->
            <div class="mb-6">
                <label for="image" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-image mr-2"></i>Image URL
                </label>
                <input 
                    type="text" 
                    id="image" 
                    name="image" 
                    value="{{ old('image', $service->image) }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="https://example.com/image.jpg"
                >
                @error('image')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="mb-6">
                <label class="flex items-center text-gray-300">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2"
                    >
                    <span class="ml-2">Active (available for booking)</span>
                </label>
                @error('is_active')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Stats -->
            <div class="mb-6 p-4 bg-gray-700 rounded-lg">
                <p class="text-gray-400 text-xs mb-2">Popularity Score</p>
                <p class="text-white font-semibold flex items-center">
                    <i class="fas fa-fire text-orange-400 mr-2"></i>
                    {{ $service->popularity ?? 0 }} bookings
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.services.index') }}" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
