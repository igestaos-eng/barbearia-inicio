<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Services
Route::resource('services', ServiceController::class)->only(['index', 'show']);

// Barbers
Route::resource('barbers', BarberController::class)->only(['index', 'show']);

// Appointments - Protected routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::resource('appointments', AppointmentController::class)->except(['index', 'show']);
});

// Guest routes (viewing appointments)
Route::resource('appointments', AppointmentController::class)->only(['index', 'show']);

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:access-admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentAdminController::class);
    Route::resource('barbers', \App\Http\Controllers\Admin\BarberAdminController::class);
    Route::resource('services', \App\Http\Controllers\Admin\ServiceAdminController::class);
});

