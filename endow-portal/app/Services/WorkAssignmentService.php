<?php

namespace App\Services;

use App\Models\WorkAssignment;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Exception;

/**
 * Work Assignment Service
 *
 * Handles business logic for work assignment operations
 */
class WorkAssignmentService
{
    /**
     * Get work assignments based on user role and filters
     */
    public function getAssignments(User $user, array $filters = [])
    {
        $query = WorkAssignment::with(['assignedTo', 'assignedBy', 'department'])
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        $this->applyFilters($query, $filters);

        // Filter by user role
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            // Super admins see all assignments
        } elseif ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            // Department managers see assignments in their departments and their own
            $managedDeptIds = $user->managedDepartments->pluck('id')->toArray();
            $query->where(function ($q) use ($user, $managedDeptIds) {
                $q->whereIn('department_id', $managedDeptIds)
                  ->orWhere('assigned_to', $user->id)
                  ->orWhere('assigned_by', $user->id);
            });
        } else {
            // Regular employees see only their assignments
            $query->where('assigned_to', $user->id);
        }

        return $query->paginate(20);
    }

    /**
     * Get assignments assigned to a specific user
     */
    public function getMyAssignments(User $user, array $filters = [])
    {
        $query = WorkAssignment::with(['assignedBy', 'department'])
            ->where('assigned_to', $user->id)
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        $this->applyFilters($query, $filters);

        return $query->paginate(20);
    }

    /**
     * Get assignments created by a specific user (manager view)
     */
    public function getCreatedAssignments(User $user, array $filters = [])
    {
        $query = WorkAssignment::with(['assignedTo', 'department'])
            ->where('assigned_by', $user->id)
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        $this->applyFilters($query, $filters);

        return $query->paginate(20);
    }

    /**
     * Get pending assignments for daily report integration
     */
    public function getPendingAssignmentsForReport(User $user, $reportDate)
    {
        return WorkAssignment::with(['assignedBy', 'department'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', [
                WorkAssignment::STATUS_PENDING,
                WorkAssignment::STATUS_IN_PROGRESS
            ])
            ->where('included_in_report', false)
            ->where(function ($query) use ($reportDate) {
                $query->whereDate('due_date', '<=', $reportDate)
                      ->orWhereNull('due_date');
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['department'])) {
            $query->where('department_id', $filters['department']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('assigned_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('assigned_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['due_date_start'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_start']);
        }

        if (!empty($filters['due_date_end'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_end']);
        }

        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->where('status', '!=', WorkAssignment::STATUS_COMPLETED)
                  ->where('status', '!=', WorkAssignment::STATUS_CANCELLED)
                  ->whereNotNull('due_date')
                  ->whereDate('due_date', '<', now());
        }
    }

    /**
     * Create a new work assignment
     */
    public function createAssignment(array $data, User $assignedBy): WorkAssignment
    {
        try {
            DB::beginTransaction();

            $assignment = WorkAssignment::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? WorkAssignment::PRIORITY_NORMAL,
                'department_id' => $data['department_id'] ?? null,
                'assigned_by' => $assignedBy->id,
                'assigned_to' => $data['assigned_to'],
                'assigned_date' => $data['assigned_date'] ?? now(),
                'due_date' => $data['due_date'] ?? null,
                'status' => WorkAssignment::STATUS_PENDING,
            ]);

            DB::commit();

            return $assignment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing work assignment
     */
    public function updateAssignment(WorkAssignment $assignment, array $data): WorkAssignment
    {
        try {
            DB::beginTransaction();

            $assignment->update([
                'title' => $data['title'] ?? $assignment->title,
                'description' => $data['description'] ?? $assignment->description,
                'priority' => $data['priority'] ?? $assignment->priority,
                'department_id' => $data['department_id'] ?? $assignment->department_id,
                'assigned_to' => $data['assigned_to'] ?? $assignment->assigned_to,
                'due_date' => $data['due_date'] ?? $assignment->due_date,
            ]);

            DB::commit();

            return $assignment->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update assignment status
     */
    public function updateStatus(WorkAssignment $assignment, string $status, ?string $notes = null): WorkAssignment
    {
        try {
            DB::beginTransaction();

            $updateData = ['status' => $status];

            // If marking as completed, set completion time
            if ($status === WorkAssignment::STATUS_COMPLETED && !$assignment->completed_at) {
                $updateData['completed_at'] = now();
            }

            // Add completion notes if provided
            if ($notes && $status === WorkAssignment::STATUS_COMPLETED) {
                $updateData['completion_notes'] = $notes;
            }

            $assignment->update($updateData);

            DB::commit();

            return $assignment->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Add employee notes to assignment
     */
    public function addEmployeeNotes(WorkAssignment $assignment, string $notes): WorkAssignment
    {
        $assignment->update(['employee_notes' => $notes]);
        return $assignment->fresh();
    }

    /**
     * Add manager feedback to assignment
     */
    public function addManagerFeedback(WorkAssignment $assignment, string $feedback): WorkAssignment
    {
        $assignment->update(['manager_feedback' => $feedback]);
        return $assignment->fresh();
    }

    /**
     * Link assignment to a daily report
     */
    public function linkToReport(WorkAssignment $assignment, int $reportId): WorkAssignment
    {
        $assignment->update([
            'daily_report_id' => $reportId,
            'included_in_report' => true,
        ]);

        return $assignment->fresh();
    }

    /**
     * Delete an assignment (soft delete)
     */
    public function deleteAssignment(WorkAssignment $assignment): bool
    {
        return $assignment->delete();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(User $user, array $filters = []): array
    {
        $query = WorkAssignment::query();

        // Filter by user role
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            // Super admins see all statistics
        } elseif ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            // Department managers see statistics for their departments
            $managedDeptIds = $user->managedDepartments->pluck('id')->toArray();
            $query->where(function ($q) use ($user, $managedDeptIds) {
                $q->whereIn('department_id', $managedDeptIds)
                  ->orWhere('assigned_to', $user->id)
                  ->orWhere('assigned_by', $user->id);
            });
        } else {
            // Regular employees see only their statistics
            $query->where('assigned_to', $user->id);
        }

        // Apply date filters if provided
        if (!empty($filters['start_date'])) {
            $query->whereDate('assigned_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('assigned_date', '<=', $filters['end_date']);
        }

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', WorkAssignment::STATUS_PENDING)->count(),
            'in_progress' => (clone $query)->where('status', WorkAssignment::STATUS_IN_PROGRESS)->count(),
            'completed' => (clone $query)->where('status', WorkAssignment::STATUS_COMPLETED)->count(),
            'on_hold' => (clone $query)->where('status', WorkAssignment::STATUS_ON_HOLD)->count(),
            'overdue' => (clone $query)->where('status', '!=', WorkAssignment::STATUS_COMPLETED)
                                       ->where('status', '!=', WorkAssignment::STATUS_CANCELLED)
                                       ->whereNotNull('due_date')
                                       ->whereDate('due_date', '<', now())
                                       ->count(),
        ];
    }

    /**
     * Get employees for assignment dropdown
     */
    public function getAvailableEmployees(User $manager): array
    {
        $query = User::where('status', 'active')
                    ->orderBy('name', 'asc');

        // If manager, get employees from their departments
        if ($manager->hasRole('department_manager') || $manager->isManagerOfAnyDepartment()) {
            $managedDeptIds = $manager->managedDepartments->pluck('id')->toArray();
            $query->where(function ($q) use ($managedDeptIds) {
                $q->whereHas('departments', function ($subQuery) use ($managedDeptIds) {
                    $subQuery->whereIn('departments.id', $managedDeptIds);
                })->orWhereIn('department_id', $managedDeptIds);
            });
        }

        return $query->get(['id', 'name', 'email'])->toArray();
    }

    /**
     * Get departments for assignment dropdown
     */
    public function getAvailableDepartments(User $user): array
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            // Super admins see all departments
            return Department::where('is_active', true)
                           ->orderBy('name', 'asc')
                           ->get(['id', 'name'])
                           ->toArray();
        } elseif ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            // Department managers see only their departments
            return $user->managedDepartments()
                       ->where('is_active', true)
                       ->orderBy('name', 'asc')
                       ->get(['id', 'name'])
                       ->toArray();
        }

        return [];
    }
}
