<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modify the role enum to include superadmin
            $table->enum('role', ['superadmin', 'admin', 'barber', 'customer'])
                ->default('customer')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to original roles
            $table->enum('role', ['admin', 'barber', 'customer'])
                ->default('customer')
                ->change();
        });
    }
};
