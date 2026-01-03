<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentRepository
{
    /**
     * Get paginated students with filters and eager loaded relationships.
     *
     * @param array $filters Available filters: status, account_status, assigned_to, search
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Student::with([
            'assignedUser:id,name,email',
            'creator:id,name'
        ])->select('students.*');

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply account status filter
        if (!empty($filters['account_status'])) {
            $query->where('account_status', $filters['account_status']);
        }

        // Apply assigned user filter
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get dashboard statistics for a user.
     * Optimized to use minimal queries with aggregation.
     *
     * @param int|null $userId If provided, only count students assigned to this user
     * @return array ['total', 'pending_approvals', 'status_counts']
     */
    public function getDashboardStats(?int $userId = null): array
    {
        $query = Student::query();

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        // Get status counts in a single query using GROUP BY
        $statusResults = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get pending approvals count
        $pendingApprovals = (clone $query)
            ->where('account_status', 'pending')
            ->count();

        // Calculate total from status counts
        $total = array_sum($statusResults);

        // Ensure all statuses are present with 0 count if missing
        $statusCounts = [
            'new' => $statusResults['new'] ?? 0,
            'contacted' => $statusResults['contacted'] ?? 0,
            'processing' => $statusResults['processing'] ?? 0,
            'applied' => $statusResults['applied'] ?? 0,
            'approved' => $statusResults['approved'] ?? 0,
            'rejected' => $statusResults['rejected'] ?? 0,
        ];

        return [
            'total' => $total,
            'pending_approvals' => $pendingApprovals,
            'status_counts' => $statusCounts,
        ];
    }

    /**
     * Get recent students with minimal data for dashboard.
     *
     * @param int|null $userId Filter by assigned user
     * @param int $limit Number of records to fetch
     * @return Collection
     */
    public function getRecent(?int $userId = null, int $limit = 10): Collection
    {
        $query = Student::select('id', 'name', 'email', 'status', 'account_status', 'assigned_to', 'created_by', 'created_at')
            ->with([
                'assignedUser:id,name,email',
                'creator:id,name'
            ]);

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        return $query->latest('created_at')->limit($limit)->get();
    }

    /**
     * Get pending approval students.
     *
     * @param int|null $userId Filter by assigned user
     * @param int $limit Number of records to fetch
     * @return Collection
     */
    public function getPendingApprovals(?int $userId = null, int $limit = 5): Collection
    {
        $query = Student::select('id', 'name', 'email', 'status', 'account_status', 'created_by', 'created_at')
            ->with(['creator:id,name'])
            ->where('account_status', 'pending');

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        return $query->latest('created_at')->limit($limit)->get();
    }

    /**
     * Get student with all related data for show page.
     * Uses single query with nested eager loading.
     *
     * @param int $studentId
     * @return Student|null
     */
    public function getWithFullDetails(int $studentId): ?Student
    {
        return Student::with([
            'assignedUser:id,name,email',
            'creator:id,name',
            'followUps' => function($query) {
                $query->latest('due_date')->limit(20);
            },
            'followUps.creator:id,name',
            'checklists.checklistItem:id,title,description',
            'checklists.documents' => function($query) {
                $query->latest('created_at');
            },
            'checklists.documents.uploader:id,name',
            'checklists.documents.reviewer:id,name',
            'checklists.reviewer:id,name',
            'documents' => function($query) {
                $query->latest('created_at');
            },
            'documents.checklistItem:id,title',
            'documents.uploader:id,name',
            'documents.reviewer:id,name',
            'targetUniversity:id,name',
            'targetProgram:id,name,university_id'
        ])->find($studentId);
    }

    /**
     * Get students count by status for quick stats.
     *
     * @param int|null $userId Filter by assigned user
     * @return array
     */
    public function getStatusCounts(?int $userId = null): array
    {
        $query = Student::select('status', DB::raw('count(*) as count'))
            ->groupBy('status');

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        $results = $query->pluck('count', 'status')->toArray();

        return [
            'new' => $results['new'] ?? 0,
            'contacted' => $results['contacted'] ?? 0,
            'processing' => $results['processing'] ?? 0,
            'applied' => $results['applied'] ?? 0,
            'approved' => $results['approved'] ?? 0,
            'rejected' => $results['rejected'] ?? 0,
        ];
    }

    /**
     * Search students by query string.
     * Searches in name, email, phone, country fields.
     *
     * @param string $searchTerm
     * @param int|null $userId Filter by assigned user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $searchTerm, ?int $userId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Student::select('students.*')
            ->with([
                'assignedUser:id,name,email',
                'creator:id,name'
            ])
            ->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('country', 'like', "%{$searchTerm}%");
            });

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get student by ID with basic relationships.
     *
     * @param int $studentId
     * @return Student|null
     */
    public function findWithBasicRelations(int $studentId): ?Student
    {
        return Student::with([
            'assignedUser:id,name,email',
            'creator:id,name',
            'targetUniversity:id,name',
            'targetProgram:id,name'
        ])->find($studentId);
    }

    /**
     * Get students assigned to a specific user.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByAssignedUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Student::select('students.*')
            ->with(['assignedUser:id,name', 'creator:id,name'])
            ->where('assigned_to', $userId)
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Bulk update status for multiple students.
     *
     * @param array $studentIds
     * @param string $status
     * @return int Number of affected rows
     */
    public function bulkUpdateStatus(array $studentIds, string $status): int
    {
        return Student::whereIn('id', $studentIds)->update(['status' => $status]);
    }

    /**
     * Get count of students grouped by account status.
     *
     * @param int|null $userId
     * @return array
     */
    public function getAccountStatusCounts(?int $userId = null): array
    {
        $query = Student::select('account_status', DB::raw('count(*) as count'))
            ->groupBy('account_status');

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        return $query->pluck('count', 'account_status')->toArray();
    }
}
