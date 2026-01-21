<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Daily Report Service
 *
 * Handles business logic for daily reports in the Office Management System.
 * Separates concerns from controllers for better maintainability.
 */
class DailyReportService
{
    /**
     * Get paginated reports with filters
     */
    public function getReports(array $filters = [], int $perPage = 15)
    {
        $query = DailyReport::with(['submittedBy', 'reviewedBy', 'department'])
            ->latest('report_date')
            ->latest('created_at');

        // Apply filters
        if (!empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['submitted_by'])) {
            $query->bySubmitter($filters['submitted_by']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get reports for a specific user (department staff view)
     */
    public function getMyReports(User $user, array $filters = [], int $perPage = 15)
    {
        $filters['submitted_by'] = $user->id;
        return $this->getReports($filters, $perPage);
    }

    /**
     * Create a new daily report
     */
    public function createReport(array $data, User $submitter): DailyReport
    {
        try {
            DB::beginTransaction();

            $report = DailyReport::create([
                'department_id' => $submitter->department_id,
                'title' => $data['title'],
                'description' => $data['description'],
                'report_date' => $data['report_date'],
                'submitted_by' => $submitter->id,
                'status' => $data['status'] ?? DailyReport::STATUS_IN_PROGRESS,
            ]);

            // Log activity
            Log::info('Daily report created', [
                'report_id' => $report->id,
                'department_id' => $report->department_id,
                'submitted_by' => $submitter->id,
            ]);

            DB::commit();

            return $report;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create daily report', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing report
     */
    public function updateReport(DailyReport $report, array $data): DailyReport
    {
        try {
            DB::beginTransaction();

            // Only allow updates if report is not completed
            if ($report->isCompleted()) {
                throw new Exception('Cannot update a completed report');
            }

            $report->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'report_date' => $data['report_date'],
                'status' => $data['status'],
            ]);

            Log::info('Daily report updated', [
                'report_id' => $report->id,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return $report->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update daily report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Review a report (admin action)
     */
    public function reviewReport(DailyReport $report, User $reviewer, ?string $comment = null): DailyReport
    {
        try {
            DB::beginTransaction();

            $report->update([
                'status' => DailyReport::STATUS_COMPLETED,
                'reviewed_by' => $reviewer->id,
                'review_comment' => $comment,
                'reviewed_at' => now(),
            ]);

            Log::info('Daily report reviewed', [
                'report_id' => $report->id,
                'reviewed_by' => $reviewer->id,
            ]);

            DB::commit();

            return $report->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to review daily report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a report (soft delete)
     */
    public function deleteReport(DailyReport $report): bool
    {
        try {
            DB::beginTransaction();

            $reportId = $report->id;
            $report->delete();

            Log::info('Daily report deleted', [
                'report_id' => $reportId,
                'deleted_by' => auth()->id(),
            ]);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete daily report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(array $filters = []): array
    {
        $query = DailyReport::query();

        // Apply date filter if provided
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        return [
            'total' => (clone $query)->count(),
            'in_progress' => (clone $query)->inProgress()->count(),
            'review' => (clone $query)->inReview()->count(),
            'completed' => (clone $query)->completed()->count(),
            'by_department' => (clone $query)
                ->with('department:id,name')
                ->get()
                ->groupBy('department.name')
                ->map(fn($group) => $group->count())
                ->toArray(),
        ];
    }
}
