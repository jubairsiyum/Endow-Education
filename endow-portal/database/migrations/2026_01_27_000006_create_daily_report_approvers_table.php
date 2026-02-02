<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Multi-level approval workflow
     * Supports hierarchical approval chains
     */
    public function up(): void
    {
        Schema::create('office_daily_report_approvers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('daily_report_id')
                ->constrained('office_daily_reports')
                ->onDelete('cascade');
            
            $table->foreignId('approver_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Approval sequence (1 = first approver, 2 = second, etc.)
            $table->unsignedTinyInteger('approval_level')->default(1);
            
            // Status: pending, approved, rejected, skipped
            $table->enum('status', ['pending', 'approved', 'rejected', 'skipped'])
                ->default('pending');
            
            $table->text('comments')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            // Notification tracking
            $table->timestamp('notified_at')->nullable();
            $table->unsignedTinyInteger('reminder_count')->default(0);
            
            $table->timestamps();
            
            // Indexes with custom short names
            $table->index('daily_report_id', 'dr_approvers_report_idx');
            $table->index(['daily_report_id', 'approval_level'], 'dr_approvers_level_idx');
            $table->index(['approver_id', 'status'], 'dr_approvers_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_report_approvers');
    }
};
