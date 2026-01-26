<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the office daily reports table for the Office Management System.
     * Departments can submit daily reports, and admins can review them.
     */
    public function up(): void
    {
        Schema::create('office_daily_reports', function (Blueprint $table) {
            $table->id();

            // Department identifier
            $table->enum('department', [
                'marketing',
                'it',
                'consultant',
                'hr',
                'logistics'
            ])->index();

            // Report content
            $table->string('title');
            $table->text('description');
            $table->date('report_date')->index();

            // User tracking
            $table->foreignId('submitted_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Status tracking
            $table->enum('status', ['pending', 'reviewed'])
                ->default('pending')
                ->index();

            // Review details
            $table->text('review_comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Safe deletion for production

            // Indexes for performance
            $table->index(['department', 'report_date']);
            $table->index(['submitted_by', 'report_date']);
            $table->index(['status', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_reports');
    }
};
