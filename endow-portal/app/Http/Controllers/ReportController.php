<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\University;
use App\Models\Program;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view reports');
    }

    /**
     * Display reports dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = null;
        
        // If employee, filter by their assigned students
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            $userId = $user->id;
        }

        // Date range filter
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Build base query
        $baseQuery = Student::query();
        if ($userId) {
            $baseQuery->where('assigned_to', $userId);
        }

        // Student Statistics
        $totalStudents = (clone $baseQuery)->count();
        $newStudents = (clone $baseQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $activeStudents = (clone $baseQuery)
            ->where('status', 'processing')
            ->count();
        $approvedStudents = (clone $baseQuery)
            ->where('account_status', 'approved')
            ->count();

        // Application Status Breakdown
        $statusBreakdown = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->total];
            });

        // Monthly Enrollments (Last 6 months)
        $monthlyEnrollments = Student::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->when($userId, function($query) use ($userId) {
                return $query->where('assigned_to', $userId);
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Universities
        $topUniversities = (clone $baseQuery)
            ->select('target_university_id', DB::raw('count(*) as total'))
            ->whereNotNull('target_university_id')
            ->groupBy('target_university_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('targetUniversity:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->targetUniversity->name ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Top Programs
        $topPrograms = (clone $baseQuery)
            ->select('target_program_id', DB::raw('count(*) as total'))
            ->whereNotNull('target_program_id')
            ->groupBy('target_program_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('targetProgram:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->targetProgram->name ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Students by Country
        $studentsByCountry = (clone $baseQuery)
            ->select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Document Processing Stats
        $totalDocuments = StudentDocument::whereHas('student', function($query) use ($userId) {
                if ($userId) {
                    $query->where('assigned_to', $userId);
                }
            })->count();

        $approvedDocuments = StudentDocument::whereHas('student', function($query) use ($userId) {
                if ($userId) {
                    $query->where('assigned_to', $userId);
                }
            })->where('status', 'approved')->count();

        $pendingDocuments = StudentDocument::whereHas('student', function($query) use ($userId) {
                if ($userId) {
                    $query->where('assigned_to', $userId);
                }
            })->where('status', 'pending')->count();

        // Conversion Rate
        $conversionRate = $totalStudents > 0 ? round(($approvedStudents / $totalStudents) * 100, 2) : 0;

        // Counselor Performance (Admin only)
        $counselorPerformance = collect();
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            $counselorPerformance = User::role(['Employee', 'Admin'])
                ->withCount(['assignedStudents'])
                ->with(['assignedStudents' => function($query) {
                    $query->select('assigned_to', 'status', DB::raw('count(*) as total'))
                        ->groupBy('assigned_to', 'status');
                }])
                ->having('assigned_students_count', '>', 0)
                ->orderByDesc('assigned_students_count')
                ->limit(10)
                ->get();
        }

        return view('reports.index', compact(
            'totalStudents',
            'newStudents',
            'activeStudents',
            'approvedStudents',
            'statusBreakdown',
            'monthlyEnrollments',
            'topUniversities',
            'topPrograms',
            'studentsByCountry',
            'totalDocuments',
            'approvedDocuments',
            'pendingDocuments',
            'conversionRate',
            'counselorPerformance',
            'startDate',
            'endDate'
        ));
    }
}
