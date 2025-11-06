<?php

namespace App\Providers;

use App\Policies\AdminPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the gate for admin access
        Gate::define('access-admin', [AdminPolicy::class, 'accessAdmin']);
        Gate::define('manage-admins', [AdminPolicy::class, 'manageAdmins']);
    }
}
