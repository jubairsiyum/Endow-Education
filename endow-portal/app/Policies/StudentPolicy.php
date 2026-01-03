<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view students');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        // Admin and Super Admin can view all students
        if ($user->isAdmin()) {
            return true;
        }

        // Employees can view assigned students
        if ($user->isEmployee()) {
            return $student->assigned_to === $user->id;
        }

        // Students can view their own profile
        if ($user->isStudent()) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Students cannot create student records
        if ($user->isStudent()) {
            return false;
        }

        return $user->can('create students');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        // Admin and Super Admin can update all students
        if ($user->isAdmin()) {
            return true;
        }

        // Employees can update assigned students
        if ($user->isEmployee()) {
            return $student->assigned_to === $user->id && $user->can('edit students');
        }

        // Students can update their own profile
        if ($user->isStudent()) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        // Only Admin and Super Admin can delete
        return $user->isAdmin() && $user->can('delete students');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->isAdmin() && $user->can('delete students');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can approve the student account.
     */
    public function approve(User $user, Student $student): bool
    {
        // Only Admin, Super Admin, and assigned Employee can approve
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEmployee() && $student->assigned_to === $user->id) {
            return $user->can('approve students');
        }

        return false;
    }

    /**
     * Determine whether the user can assign students.
     */
    public function assign(User $user): bool
    {
        // Only Admin and Super Admin can reassign
        return $user->isAdmin() && $user->can('assign students');
    }
}
