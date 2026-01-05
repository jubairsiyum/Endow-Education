<?php

namespace App\Policies;

use App\Models\StudentVisit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentVisitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any student visits.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view visits
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the student visit.
     */
    public function view(User $user, StudentVisit $studentVisit): bool
    {
        // Admins can view all, employees can only view their own
        return $user->isAdmin() || $studentVisit->employee_id === $user->id;
    }

    /**
     * Determine whether the user can create student visits.
     */
    public function create(User $user): bool
    {
        // Only admins can create visits (employees read-only)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the student visit.
     */
    public function update(User $user, StudentVisit $studentVisit): bool
    {
        // Only admins can update visits (employees read-only)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the student visit.
     */
    public function delete(User $user, StudentVisit $studentVisit): bool
    {
        // Only admins can delete visits (employees read-only)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the student visit.
     */
    public function restore(User $user, StudentVisit $studentVisit): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the student visit.
     */
    public function forceDelete(User $user, StudentVisit $studentVisit): bool
    {
        return $user->isSuperAdmin();
    }
}
