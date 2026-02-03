<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the pivot table for many-to-many relationship
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['department_id', 'user_id']);
            
            // Indexes for performance
            $table->index('department_id');
            $table->index('user_id');
        });

        // Migrate existing data from users.department_id to pivot table
        DB::statement('
            INSERT INTO department_user (department_id, user_id, created_at, updated_at)
            SELECT department_id, id, NOW(), NOW()
            FROM users
            WHERE department_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_user');
    }
};
