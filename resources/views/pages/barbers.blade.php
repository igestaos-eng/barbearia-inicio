@extends('layouts.app')

@section('title', 'Our Barbers - ' . config('barbershop.name'))

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-indigo-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white">Meet Our Expert Barbers</h1>
            <p class="mt-4 text-xl text-indigo-100">Professional stylists dedicated to your perfect look</p>
        </div>
    </div>

    <!-- Barbers Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($barbers as $barber)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    @if($barber->photo)
                        <img src="{{ asset('storage/' . $barber->photo) }}" alt="{{ $barber->user->name }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                            <span class="text-white text-6xl font-bold">{{ substr($barber->user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $barber->user->name }}</h3>
                        
                        @if($barber->specialization)
                            <p class="text-indigo-600 font-medium mt-1">{{ $barber->specialization }}</p>
                        @endif
                        
                        <div class="flex items-center mt-3">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5 {{ $i <= $barber->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">{{ number_format($barber->rating, 1) }} ({{ $barber->total_reviews }} reviews)</span>
                        </div>
                        
                        <p class="text-gray-600 mt-4">{{ Str::limit($barber->bio, 120) }}</p>
                        
                        <div class="mt-4 text-sm text-gray-500">
                            <span class="inline-flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $barber->experience_years }} years experience
                            </span>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('appointments.create', ['barber_id' => $barber->id]) }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Book with {{ $barber->user->name }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No barbers available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
