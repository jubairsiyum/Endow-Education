<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Role-based dashboard redirect
        if ($user->hasRole('Student')) {
            return $this->studentDashboard();
        }

        // Admin, Super Admin, and Employee get the admin dashboard
        return $this->adminDashboard();
    }

    public function adminDashboard()
    {
        $user = Auth::user();

        // Build query based on role
        $query = Student::query();

        // Employees only see their assigned students
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            $query->where('assigned_to', $user->id);
        }

        // Statistics
        $totalStudents = (clone $query)->count();
        $pendingApprovals = (clone $query)->where('account_status', 'pending')->count();
        
        $statusCounts = [
            'new' => (clone $query)->where('status', 'new')->count(),
            'contacted' => (clone $query)->where('status', 'contacted')->count(),
            'processing' => (clone $query)->where('status', 'processing')->count(),
            'applied' => (clone $query)->where('status', 'applied')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        // Recent students (last 10)
        $recentStudents = (clone $query)
            ->with(['user', 'assignedUser', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        // Pending approvals list
        $pendingStudents = (clone $query)
            ->where('account_status', 'pending')
            ->with(['user', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalStudents',
            'pendingApprovals',
            'statusCounts',
            'recentStudents',
            'pendingStudents'
        ));
    }

    public function studentDashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('students.create')
                ->with('warning', 'Please complete your profile to continue.');
        }

        return view('dashboard.student', compact('student'));
    }
}
