<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Enhances the daily reports table with professional features:
     * - Draft status for work-in-progress reports
     * - Priority levels for urgent reports
     * - Rejection workflow with feedback
     * - Better status tracking (pending_review, approved, rejected)
     * - Submission and approval timestamps
     * - Tags for better categorization
     * - Attachments support
     */
    public function up(): void
    {
        Schema::table('office_daily_reports', function (Blueprint $table) {
            // Add priority field
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                ->default('normal')
                ->after('status')
                ->index();
            
            // Add approval workflow fields
            $table->foreignId('approved_by')
                ->nullable()
                ->after('reviewed_by')
                ->constrained('users')
                ->onDelete('set null');
            
            $table->timestamp('submitted_at')->nullable()->after('report_date');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->text('rejection_reason')->nullable()->after('review_comment');
            
            // Add tags for categorization (JSON field)
            $table->json('tags')->nullable()->after('description');
            
            // Add parent report for follow-ups
            $table->foreignId('parent_report_id')
                ->nullable()
                ->after('department_id')
                ->constrained('office_daily_reports')
                ->onDelete('set null');
            
            // Add is_template flag for recurring reports
            $table->boolean('is_template')->default(false)->after('status');
            
            // Add estimated completion time
            $table->date('estimated_completion_date')->nullable()->after('report_date');
            
            // Add indexes for performance
            $table->index(['priority', 'status']);
            $table->index('submitted_at');
            $table->index('approved_at');
        });

        // Update existing enum to include new statuses
        DB::statement("ALTER TABLE office_daily_reports MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'pending_review',
            'in_progress', 
            'review',
            'approved',
            'rejected',
            'completed',
            'cancelled'
        ) DEFAULT 'draft'");

        // Migrate existing data
        DB::statement("UPDATE office_daily_reports SET status = 'submitted' WHERE status = 'in_progress' AND submitted_at IS NULL");
        DB::statement("UPDATE office_daily_reports SET status = 'approved' WHERE status = 'completed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_daily_reports', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['parent_report_id']);
            
            $table->dropColumn([
                'priority',
                'approved_by',
                'submitted_at',
                'approved_at',
                'rejection_reason',
                'tags',
                'parent_report_id',
                'is_template',
                'estimated_completion_date',
            ]);
        });

        // Restore original enum
        DB::statement("ALTER TABLE office_daily_reports MODIFY COLUMN status ENUM('in_progress', 'review', 'completed') DEFAULT 'in_progress'");
    }
};
