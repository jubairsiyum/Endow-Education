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
     * Only superiors (Super Admin, Admin, HR, office_admin) can review
     * Users cannot review their own reports
     */
    public function review(User $user, DailyReport $report): bool
    {
        // Cannot review own reports
        if ($report->submitted_by === $user->id) {
            return false;
        }

        // Super Admin, Admin, HR, and office admins can review
        return $user->hasAnyRole(['Super Admin', 'Admin', 'HR', 'office_admin']) ||
               $user->hasPermission('review daily reports');
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
}
