<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Daily Report Service
 *
 * Handles business logic for daily reports in the Office Management System.
 * Professional implementation with workflow management, audit logging, and approval chains.
 */
class DailyReportService
{
    protected DailyReportActivityLogger $activityLogger;
    protected DailyReportApprovalService $approvalService;

    public function __construct(
        DailyReportActivityLogger $activityLogger,
        DailyReportApprovalService $approvalService
    ) {
        $this->activityLogger = $activityLogger;
        $this->approvalService = $approvalService;
    }
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

        return $query->paginate($perPage);
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
                'status' => $data['status'] ?? DailyReport::STATUS_DRAFT,
                'priority' => $data['priority'] ?? DailyReport::PRIORITY_NORMAL,
                'tags' => $data['tags'] ?? null,
                'estimated_completion_date' => $data['estimated_completion_date'] ?? null,
                'parent_report_id' => $data['parent_report_id'] ?? null,
                'submitted_at' => isset($data['submit_now']) && $data['submit_now'] ? now() : null,
            ]);

            // Log activity
            $this->activityLogger->logCreated($report, $submitter->id);
            
            // If submitted immediately, update status
            if (isset($data['submit_now']) && $data['submit_now']) {
                $report->update(['status' => DailyReport::STATUS_SUBMITTED]);
                $this->activityLogger->logSubmitted($report, $submitter->id);
            }

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

            // Only allow updates if report is in editable status
            $editableStatuses = [
                DailyReport::STATUS_DRAFT,
                DailyReport::STATUS_SUBMITTED,
                'in_progress', // Legacy status
                'review' // Legacy status
            ];
            
            if (!in_array($report->status, $editableStatuses)) {
                throw new Exception('Cannot update a report in current status: ' . $report->status . '. Only draft, submitted, in_progress, and review reports can be edited.');
            }

            // Track changes for audit
            $oldData = $report->only(['title', 'description', 'status', 'priority']);

            $report->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'report_date' => $data['report_date'],
                'status' => $data['status'] ?? $report->status,
                'priority' => $data['priority'] ?? $report->priority,
                'tags' => $data['tags'] ?? $report->tags,
                'estimated_completion_date' => $data['estimated_completion_date'] ?? $report->estimated_completion_date,
            ]);

            // Log activity with changes
            $newData = $report->only(['title', 'description', 'status', 'priority']);
            $changes = array_diff_assoc($newData, $oldData);
            
            if (!empty($changes)) {
                $this->activityLogger->logUpdated($report, auth()->id(), $changes);
            }

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
    public function reviewReport(DailyReport $report, User $reviewer, ?string $comment = null, bool $markAsCompleted = false): DailyReport
    {
        try {
            DB::beginTransaction();

            // Create review history record
            $report->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'comment' => $comment,
                'marked_as_completed' => $markAsCompleted,
                'reviewed_at' => now(),
            ]);

            $updateData = [
                'reviewed_by' => $reviewer->id,
                'review_comment' => $comment,
                'reviewed_at' => now(),
            ];

            // Only update status to completed if explicitly marked
            if ($markAsCompleted) {
                $updateData['status'] = DailyReport::STATUS_COMPLETED;
            }

            $report->update($updateData);

            // Log activity
            $this->activityLogger->logReviewed($report, $reviewer->id, $comment);

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
     * Submit report for approval
     */
    public function submitReport(DailyReport $report, ?array $approvers = null): DailyReport
    {
        try {
            DB::beginTransaction();

            $report->update([
                'status' => DailyReport::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            // Initialize approval chain if approvers provided
            if ($approvers && !empty($approvers)) {
                $this->approvalService->initializeApprovalChain($report, $approvers);
            }

            // Log activity
            $this->activityLogger->logSubmitted($report, $report->submitted_by);

            DB::commit();

            return $report->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Approve a report
     */
    public function approveReport(DailyReport $report, User $approver, ?string $comment = null): DailyReport
    {
        try {
            DB::beginTransaction();

            // Process through approval service
            $approved = $this->approvalService->processApproval($report, $approver, true, $comment);

            if ($approved) {
                $this->activityLogger->logApproved($report, $approver->id, $comment);
            }

            DB::commit();

            return $report->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reject a report
     */
    public function rejectReport(DailyReport $report, User $rejector, string $reason): DailyReport
    {
        try {
            DB::beginTransaction();

            // Update report status
            $report->update([
                'status' => DailyReport::STATUS_REJECTED,
                'rejection_reason' => $reason,
                'reviewed_by' => $rejector->id,
                'reviewed_at' => now(),
            ]);

            // Log activity
            $this->activityLogger->logRejected($report, $rejector->id, $reason);

            DB::commit();

            return $report->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject report', [
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
            
            // Log before deletion
            $this->activityLogger->logDeleted($report, auth()->id());
            
            $report->delete();

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

        // Apply department filter
        if (!empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        return [
            'total' => (clone $query)->count(),
            'draft' => (clone $query)->where('status', DailyReport::STATUS_DRAFT)->count(),
            'submitted' => (clone $query)->where('status', DailyReport::STATUS_SUBMITTED)->count(),
            'pending_review' => (clone $query)->where('status', DailyReport::STATUS_PENDING_REVIEW)->count(),
            'in_progress' => (clone $query)->where('status', DailyReport::STATUS_IN_PROGRESS)->count(),
            'review' => (clone $query)->where('status', DailyReport::STATUS_REVIEW)->count(),
            'approved' => (clone $query)->where('status', DailyReport::STATUS_APPROVED)->count(),
            'rejected' => (clone $query)->where('status', DailyReport::STATUS_REJECTED)->count(),
            'completed' => (clone $query)->where('status', DailyReport::STATUS_COMPLETED)->count(),
            'by_priority' => [
                'urgent' => (clone $query)->where('priority', DailyReport::PRIORITY_URGENT)->count(),
                'high' => (clone $query)->where('priority', DailyReport::PRIORITY_HIGH)->count(),
                'normal' => (clone $query)->where('priority', DailyReport::PRIORITY_NORMAL)->count(),
                'low' => (clone $query)->where('priority', DailyReport::PRIORITY_LOW)->count(),
            ],
            'by_department' => (clone $query)
                ->with('department:id,name')
                ->get()
                ->groupBy('department.name')
                ->map(fn($group) => $group->count())
                ->toArray(),
            'overdue' => (clone $query)
                ->whereNotNull('estimated_completion_date')
                ->where('estimated_completion_date', '<', now())
                ->whereNotIn('status', [DailyReport::STATUS_COMPLETED, DailyReport::STATUS_CANCELLED])
                ->count(),
        ];
    }

    /**
     * Add attachment to report
     */
    public function addAttachment(DailyReport $report, array $fileData, User $uploader): void
    {
        DB::beginTransaction();
        try {
            $report->attachments()->create([
                'file_name' => $fileData['file_name'],
                'file_path' => $fileData['file_path'],
                'file_type' => $fileData['file_type'],
                'file_size' => $fileData['file_size'],
                'mime_type' => $fileData['mime_type'],
                'uploaded_by' => $uploader->id,
            ]);

            $this->activityLogger->logAttachmentAdded($report, $uploader->id, $fileData['file_name']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add attachment', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Add comment to report
     */
    public function addComment(DailyReport $report, User $user, string $comment, string $type = 'feedback', bool $isInternal = false): void
    {
        DB::beginTransaction();
        try {
            $report->comments()->create([
                'user_id' => $user->id,
                'comment' => $comment,
                'type' => $type,
                'is_internal' => $isInternal,
            ]);

            $this->activityLogger->logCommentAdded($report, $user->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add comment', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get reports pending user's approval
     */
    public function getMyPendingApprovals(User $user, int $perPage = 15)
    {
        return $this->approvalService->getPendingApprovalsForUser($user, $perPage);
    }

    /**
     * Get reports for export with filters
     */
    public function getReportsForExport(array $filters = [])
    {
        $query = DailyReport::with(['submittedBy', 'reviewedBy', 'department', 'approvedBy'])
            ->latest('report_date')
            ->latest('created_at');

        // Apply user filter
        if (!empty($filters['user_id'])) {
            $query->where('submitted_by', $filters['user_id']);
        }

        // Apply department filter
        if (!empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Apply priority filter
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Apply date range filter
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        return $query->get();
    }
}
