<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
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

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login, forgot password)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    });

    // Authenticated admin routes
    Route::middleware(['auth', 'can:access-admin'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Resource routes
        Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentAdminController::class);
        Route::resource('barbers', \App\Http\Controllers\Admin\BarberAdminController::class);
        Route::resource('services', \App\Http\Controllers\Admin\ServiceAdminController::class);

        // User management routes (only for superadmin)
        Route::middleware('can:manage-admins')->group(function () {
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        });
    });
});
