@extends('layouts.app')

@section('title', 'Our Services - ' . config('barbershop.name'))

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-indigo-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white">Our Services</h1>
            <p class="mt-4 text-xl text-indigo-100">Premium grooming services tailored to your style</p>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                            <span class="text-6xl">{{ $service->service_type->icon() }}</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $service->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $service->service_type->label() }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mt-2 mb-4">{{ Str::limit($service->description, 100) }}</p>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-2xl font-bold text-indigo-600">
                                ${{ number_format($service->price, 2) }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $service->duration_minutes }} min
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('appointments.create', ['service_id' => $service->id]) }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No services available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
