<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\DailyReportActivityLog;
use Illuminate\Support\Facades\Log;

/**
 * Daily Report Activity Logger Service
 * 
 * Centralized logging service for audit trail
 */
class DailyReportActivityLogger
{
    /**
     * Log report creation
     */
    public function logCreated(DailyReport $report, int $userId): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'created',
            description: "Report created: {$report->title}",
            metadata: [
                'status' => $report->status,
                'priority' => $report->priority,
            ]
        );
    }

    /**
     * Log report update
     */
    public function logUpdated(DailyReport $report, int $userId, array $changes = []): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'updated',
            description: "Report updated",
            changes: $changes
        );
    }

    /**
     * Log report submission
     */
    public function logSubmitted(DailyReport $report, int $userId): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'submitted',
            description: "Report submitted for review"
        );
    }

    /**
     * Log report review
     */
    public function logReviewed(DailyReport $report, int $reviewerId, ?string $comment = null): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $reviewerId,
            action: 'reviewed',
            description: "Report reviewed by " . auth()->user()->name,
            metadata: [
                'has_comment' => !empty($comment),
            ]
        );
    }

    /**
     * Log report approval
     */
    public function logApproved(DailyReport $report, int $approverId, ?string $comment = null): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $approverId,
            action: 'approved',
            description: "Report approved",
            metadata: [
                'approver_comment' => $comment,
            ]
        );
    }

    /**
     * Log report rejection
     */
    public function logRejected(DailyReport $report, int $rejectorId, string $reason): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $rejectorId,
            action: 'rejected',
            description: "Report rejected",
            metadata: [
                'rejection_reason' => $reason,
            ]
        );
    }

    /**
     * Log report deletion
     */
    public function logDeleted(DailyReport $report, int $userId): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'deleted',
            description: "Report deleted: {$report->title}"
        );
    }

    /**
     * Log status change
     */
    public function logStatusChange(DailyReport $report, int $userId, string $oldStatus, string $newStatus): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'status_changed',
            description: "Status changed from {$oldStatus} to {$newStatus}",
            changes: [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );
    }

    /**
     * Log attachment upload
     */
    public function logAttachmentAdded(DailyReport $report, int $userId, string $fileName): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'attachment_added',
            description: "Attachment added: {$fileName}"
        );
    }

    /**
     * Log comment added
     */
    public function logCommentAdded(DailyReport $report, int $userId): void
    {
        DailyReportActivityLog::log(
            reportId: $report->id,
            userId: $userId,
            action: 'comment_added',
            description: "New comment added"
        );
    }
}
