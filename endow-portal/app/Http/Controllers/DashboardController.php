<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Role-based dashboard redirect
        if ($user->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        // Admin, Super Admin, and Employee get the admin dashboard
        return $this->adminDashboard();
    }

    public function adminDashboard()
    {
        $user = Auth::user();

        // Determine user ID for filtering (employees see only their students)
        $userId = null;
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            $userId = $user->id;
        }

        // Get all statistics in optimized queries (2-3 queries total instead of 8-10)
        $stats = $this->studentRepository->getDashboardStats($userId);
        
        // Get recent students with only needed columns and relationships
        $recentStudents = $this->studentRepository->getRecent($userId, 10);

        // Get pending approvals list
        $pendingStudents = $this->studentRepository->getPendingApprovals($userId, 5);

        return view('dashboard.admin', [
            'totalStudents' => $stats['total'],
            'pendingApprovals' => $stats['pending_approvals'],
            'statusCounts' => $stats['status_counts'],
            'recentStudents' => $recentStudents,
            'pendingStudents' => $pendingStudents,
        ]);
    }

    public function studentDashboard()
    {
        $user = Auth::user();
        
        // Optimized query with withCount for aggregates
        $student = Student::where('user_id', $user->id)
            ->with([
                'checklists' => function($query) {
                    $query->select('id', 'student_id', 'checklist_item_id', 'status')
                        ->with('checklistItem:id,title,description');
                },
                'documents' => function($query) {
                    $query->select('id', 'student_id', 'checklist_item_id', 'status', 'file_path', 'created_at')
                        ->latest('created_at')
                        ->limit(10); // Only recent documents for dashboard
                },
                'assignedUser:id,name,email'
            ])
            ->withCount([
                'checklists',
                'checklists as approved_checklists_count' => function($query) {
                    $query->where('status', 'approved');
                },
                'checklists as pending_checklists_count' => function($query) {
                    $query->whereIn('status', ['pending', 'submitted']);
                }
            ])
            ->first();

        if (!$student) {
            return redirect()->route('students.create')
                ->with('warning', 'Please complete your profile to continue.');
        }

        // Calculate checklist progress using withCount results
        $student->checklist_progress = [
            'percentage' => $student->checklists_count > 0 
                ? round(($student->approved_checklists_count / $student->checklists_count) * 100) 
                : 0,
            'approved' => $student->approved_checklists_count,
            'pending' => $student->pending_checklists_count,
        ];

        return view('dashboard.student', compact('student'));
    }
}
