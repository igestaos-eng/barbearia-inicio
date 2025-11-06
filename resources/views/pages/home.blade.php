@extends('layouts.app')

@section('title', 'Home - ' . config('barbershop.name'))

@section('content')
<div class="bg-white">
    <!-- Hero Section -->
    <div class="relative bg-indigo-600">
        <div class="max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Welcome to {{ config('barbershop.name') }}
            </h1>
            <p class="mt-6 max-w-3xl text-xl text-indigo-100">
                Experience premium grooming services from our expert barbers. Book your appointment today and look your best.
            </p>
            <div class="mt-10">
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                    Book an Appointment
                </a>
                <a href="{{ route('services.index') }}" class="ml-4 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-400">
                    View Services
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Why Choose Us
                </h2>
                <p class="mt-4 text-xl text-gray-600">
                    Professional service with a personal touch
                </p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-indigo-600 text-4xl mb-4">✂️</div>
                    <h3 class="text-lg font-medium text-gray-900">Expert Barbers</h3>
                    <p class="mt-2 text-gray-600">Highly skilled professionals with years of experience</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-indigo-600 text-4xl mb-4">📅</div>
                    <h3 class="text-lg font-medium text-gray-900">Easy Booking</h3>
                    <p class="mt-2 text-gray-600">Schedule appointments online 24/7</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-indigo-600 text-4xl mb-4">⭐</div>
                    <h3 class="text-lg font-medium text-gray-900">Quality Service</h3>
                    <p class="mt-2 text-gray-600">Premium products and attention to detail</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Ready for a fresh look?</span>
                <span class="block text-indigo-200">Book your appointment today.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('appointments.create') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
