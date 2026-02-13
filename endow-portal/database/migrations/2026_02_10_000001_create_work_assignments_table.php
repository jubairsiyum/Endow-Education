<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the work assignments table for the Work Assignment module.
     * Super admins/Department managers can assign tasks to employees.
     */
    public function up(): void
    {
        Schema::create('work_assignments', function (Blueprint $table) {
            $table->id();

            // Task details
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->index();
            
            // Department assignment
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('cascade');

            // Assignment details
            $table->foreignId('assigned_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('assigned_to')
                ->constrained('users')
                ->onDelete('cascade');

            // Dates
            $table->date('assigned_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->datetime('completed_at')->nullable();

            // Status tracking
            $table->enum('status', [
                'pending',
                'in_progress', 
                'completed',
                'on_hold',
                'cancelled'
            ])->default('pending')->index();

            // Notes and feedback
            $table->text('employee_notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('manager_feedback')->nullable();

            // Daily report integration
            $table->foreignId('daily_report_id')
                ->nullable()
                ->constrained('office_daily_reports')
                ->onDelete('set null');
            $table->boolean('included_in_report')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['assigned_to', 'status', 'due_date']);
            $table->index(['assigned_by', 'assigned_date']);
            $table->index(['department_id', 'status']);
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_assignments');
    }
};
