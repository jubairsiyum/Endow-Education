<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing review comments to the new reviews table
        $reports = DB::table('office_daily_reports')
            ->whereNotNull('review_comment')
            ->whereNotNull('reviewed_by')
            ->whereNotNull('reviewed_at')
            ->get();

        foreach ($reports as $report) {
            DB::table('office_daily_report_reviews')->insert([
                'daily_report_id' => $report->id,
                'reviewer_id' => $report->reviewed_by,
                'comment' => $report->review_comment,
                'marked_as_completed' => $report->status === 'completed',
                'reviewed_at' => $report->reviewed_at,
                'created_at' => $report->reviewed_at,
                'updated_at' => $report->reviewed_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove migrated reviews
        DB::table('office_daily_report_reviews')->truncate();
    }
};
