<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates comprehensive audit trail for all report activities
     * Tracks all changes for compliance and accountability
     */
    public function up(): void
    {
        Schema::create('office_daily_report_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                ->constrained('office_daily_reports')
                ->onDelete('cascade');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Activity types: created, updated, submitted, reviewed, approved, rejected, deleted, restored
            $table->string('action', 50)->index();
            
            $table->text('description')->nullable();
            
            // Store what changed (JSON)
            $table->json('changes')->nullable();
            
            // Store metadata (IP, user agent, etc.)
            $table->json('metadata')->nullable();
            
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamp('performed_at')->useCurrent();
            
            // Indexes with custom short names
            $table->index('daily_report_id', 'dr_activity_report_idx');
            $table->index(['daily_report_id', 'performed_at'], 'dr_activity_report_date_idx');
            $table->index('action', 'dr_activity_action_idx');
            $table->index('performed_at', 'dr_activity_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_report_activity_logs');
    }
};
