<?php

namespace App\Policies;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Daily Report Policy
 *
 * Manages authorization for daily report operations.
 * Enforces role-based access control for the Office Management System.
 */
class DailyReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any reports
     * Office admins can view all, department staff can view their own
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'manage office',
            'view daily reports',
        ]) || $user->hasAnyRole([
            'Super Admin',
            'Admin',
            'office_admin',
            'department_manager',
        ]);
    }

    /**
     * Determine if user can view a specific report
     */
    public function view(User $user, DailyReport $report): bool
    {
        // Office admins can view all reports
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'office_admin'])) {
            return true;
        }

        // Users can view their own reports
        return $report->submitted_by === $user->id;
    }

    /**
     * Determine if user can create reports
     * Any staff member can create reports for their department
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission([
            'manage office',
            'create daily reports',
        ]) || $user->hasAnyRole([
            'Super Admin',
            'Admin',
            'Employee',
            'office_admin',
            'department_manager',
            'staff',
        ]);
    }

    /**
     * Determine if user can update a report
     * Only the creator can update, and only if not yet reviewed
     */
    public function update(User $user, DailyReport $report): bool
    {
        // Cannot update reviewed reports
        if ($report->isReviewed()) {
            return false;
        }

        // Office admins can update any pending report
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'office_admin'])) {
            return true;
        }

        // Users can only update their own pending reports
        return $report->submitted_by === $user->id;
    }

    /**
     * Determine if user can delete a report
     */
    public function delete(User $user, DailyReport $report): bool
    {
        // Only office admins and super admins can delete
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'office_admin'])) {
            return true;
        }

        // Users can delete their own pending reports
        return $report->submitted_by === $user->id && $report->isPending();
    }

    /**
     * Determine if user can review reports
     * 
     * ORGANIZATIONAL HIERARCHY:
     * - Managers/Supervisors review reports from their team members
     * - Users CANNOT review their own reports (conflict of interest)
     * - Super Admin, Admin, HR, office admins can review all reports
     * - Department managers can review reports from their department
     */
    public function review(User $user, DailyReport $report): bool
    {
        // CRITICAL: Users cannot review their own reports
        if ($report->submitted_by === $user->id) {
            return false;
        }

        // Only submitted reports (not drafts) can be reviewed
        if ($report->isDraft()) {
            return false;
        }

        // Super Admin, Admin, HR, office admins can review any submitted report
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'HR', 'office_admin'])) {
            return true;
        }

        // Department managers can review reports from their department
        if ($user->hasRole('department_manager') && $user->department_id === $report->department_id) {
            return true;
        }

        // Check explicit permission
        if ($user->hasPermission('review daily reports')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user can submit a report
     */
    public function submit(User $user, DailyReport $report): bool
    {
        // Can only submit own drafts or rejected reports
        if ($report->submitted_by !== $user->id) {
            return false;
        }

        // Can submit if draft or rejected
        return in_array($report->status, [
            DailyReport::STATUS_DRAFT,
            DailyReport::STATUS_REJECTED,
        ]);
    }

    /**
     * Determine if user can approve a report
     */
    public function approve(User $user, DailyReport $report): bool
    {
        // Cannot approve own reports
        if ($report->submitted_by === $user->id) {
            return false;
        }

        // Must be in appropriate status
        if (!in_array($report->status, [
            DailyReport::STATUS_SUBMITTED,
            DailyReport::STATUS_PENDING_REVIEW,
            DailyReport::STATUS_REVIEW,
        ])) {
            return false;
        }

        // Check role-based access
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'office_admin'])) {
            return true;
        }

        // Department managers can approve reports from their department
        if ($user->hasRole('department_manager') && $user->department_id === $report->department_id) {
            return true;
        }

        // Check if user is in approval chain
        if (app(\App\Services\DailyReportApprovalService::class)->canApprove($report, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user can reject a report
     */
    public function reject(User $user, DailyReport $report): bool
    {
        // Same rules as approve
        return $this->approve($user, $report);
    }

    /**
     * Determine if user can view statistics
     */
    public function viewStatistics(User $user): bool
    {
        return $user->hasAnyRole([
            'Super Admin',
            'Admin',
            'office_admin',
            'department_manager',
        ]);
    }

    /**
     * Determine if user can view attachments
     */
    public function viewAttachments(User $user, DailyReport $report): bool
    {
        // Can view if can view the report
        return $this->view($user, $report);
    }

    /**
     * Determine if user can add attachments
     */
    public function addAttachments(User $user, DailyReport $report): bool
    {
        // Report owner can add while in editable status
        if ($report->submitted_by === $user->id && $report->canBeEdited()) {
            return true;
        }

        // Reviewers can add supporting documents
        return $this->review($user, $report);
    }

    /**
     * Determine if user can view comments
     */
    public function viewComments(User $user, DailyReport $report): bool
    {
        // Can view if can view the report
        return $this->view($user, $report);
    }

    /**
     * Determine if user can add comments
     */
    public function addComments(User $user, DailyReport $report): bool
    {
        // Can comment if can view
        return $this->view($user, $report);
    }

    /**
     * Determine if user can view activity logs
     */
    public function viewActivityLog(User $user, DailyReport $report): bool
    {
        // Only admins and report owner
        if ($report->submitted_by === $user->id) {
            return true;
        }

        return $user->hasAnyRole(['Super Admin', 'Admin', 'office_admin', 'department_manager']);
    }
}
