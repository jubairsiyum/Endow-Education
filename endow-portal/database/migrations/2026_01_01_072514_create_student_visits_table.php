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
        Schema::create('student_visits', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('prospective_status')->default('prospective_warm');
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->longText('notes')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('employee_id');
            $table->index('created_at');
            $table->index('prospective_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_visits');
    }
};
