<?php

namespace App\Policies;

use App\Models\WorkAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Work Assignment Policy
 *
 * Manages authorization for work assignment operations.
 * Enforces role-based access control for the Work Assignment module.
 */
class WorkAssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any work assignments
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view work assignments
        // They will see filtered results based on their role
        return true;
    }

    /**
     * Determine if user can view a specific work assignment
     */
    public function view(User $user, WorkAssignment $workAssignment): bool
    {
        // Super admins can view all assignments
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // Department managers can view assignments in their departments
        if ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            if ($workAssignment->department_id && $user->isManagerOfDepartment($workAssignment->department_id)) {
                return true;
            }
        }

        // Users can view assignments they created or assigned to them
        return $workAssignment->assigned_by === $user->id || 
               $workAssignment->assigned_to === $user->id;
    }

    /**
     * Determine if user can create work assignments
     * Only Super Admins and Department Managers can assign tasks
     * Regular employees CANNOT create or assign tasks
     */
    public function create(User $user): bool
    {
        // Only Super Admins and Department Managers can create work assignments
        // Regular employees are explicitly denied
        return $user->hasAnyRole(['Super Admin']) || 
               $user->hasRole('department_manager') || 
               $user->isManagerOfAnyDepartment();
    }

    /**
     * Determine if user can update a work assignment
     * ONLY Super Admins and Department Managers can edit tasks
     * Regular employees CANNOT edit tasks, even if they created them
     */
    public function update(User $user, WorkAssignment $workAssignment): bool
    {
        // Super admins can update any assignment
        if ($user->hasAnyRole(['Super Admin'])) {
            return true;
        }

        // Department managers can ONLY update assignments in their departments
        if ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            // If assignment has a department, verify manager has authority
            if ($workAssignment->department_id && $user->isManagerOfDepartment($workAssignment->department_id)) {
                return true;
            }
            // If no department assigned, only allow if the manager created it
            if (!$workAssignment->department_id && $workAssignment->assigned_by === $user->id) {
                return true;
            }
        }

        // Regular employees CANNOT update assignments
        return false;
    }

    /**
     * Determine if user can delete a work assignment
     * ONLY Super Admins and Department Managers can delete tasks
     * Regular employees CANNOT delete tasks, even if they created them
     */
    public function delete(User $user, WorkAssignment $workAssignment): bool
    {
        // Super admins can delete any assignment
        if ($user->hasAnyRole(['Super Admin'])) {
            return true;
        }

        // Department managers can ONLY delete assignments in their departments (if not completed)
        if (($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) && !$workAssignment->isCompleted()) {
            // If assignment has a department, verify manager has authority
            if ($workAssignment->department_id && $user->isManagerOfDepartment($workAssignment->department_id)) {
                return true;
            }
            // If no department assigned, only allow if the manager created it
            if (!$workAssignment->department_id && $workAssignment->assigned_by === $user->id) {
                return true;
            }
        }

        // Regular employees CANNOT delete assignments
        return false;
    }

    /**
     * Determine if user can update the status of an assignment
     * Employees can update status of their own assignments
     */
    public function updateStatus(User $user, WorkAssignment $workAssignment): bool
    {
        // Super admins can always update status
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // Department managers can update status of assignments in their departments
        if ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            if ($workAssignment->department_id && $user->isManagerOfDepartment($workAssignment->department_id)) {
                return true;
            }
        }

        // The assigned employee can update the status
        if ($workAssignment->assigned_to === $user->id) {
            return true;
        }

        // The user who created the assignment can update status
        if ($workAssignment->assigned_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user can add notes to an assignment
     */
    public function addNotes(User $user, WorkAssignment $workAssignment): bool
    {
        // Super admins can add notes
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // The assigned employee can add notes
        if ($workAssignment->assigned_to === $user->id) {
            return true;
        }

        // The manager who assigned it can add feedback
        if ($workAssignment->assigned_by === $user->id) {
            return true;
        }

        return false;
    }
}
