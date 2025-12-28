<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        // Check if user has permission to view activity logs
        if (!Auth::user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $query = ActivityLog::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filter by log name/type
        if ($request->has('log_name') && $request->log_name != '') {
            $query->where('log_name', $request->log_name);
        }

        // Filter by student
        if ($request->has('student_id') && $request->student_id != '') {
            $query->where(function($q) use ($request) {
                $q->where('subject_type', 'App\\Models\\Student')
                  ->where('subject_id', $request->student_id)
                  ->orWhere(function($subq) use ($request) {
                      $subq->where('causer_type', 'App\\Models\\Student')
                           ->where('causer_id', $request->student_id);
                  });
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by IP address
        if ($request->has('ip_address') && $request->ip_address != '') {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Search in description
        if ($request->has('search') && $request->search != '') {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20);

        // Get filter options
        $logTypes = ActivityLog::distinct()->pluck('log_name');
        $students = Student::select('id', 'name', 'email')->orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'logTypes', 'students'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        // Check if user has permission
        if (!Auth::user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $activityLog->load(['causer', 'subject']);

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Get activity logs for a specific student (for student show page)
     */
    public function studentLogs($studentId, Request $request)
    {
        // Check if user has permission
        if (!Auth::user()->hasRole(['Super Admin', 'Admin', 'Employee'])) {
            abort(403, 'Unauthorized action.');
        }

        $student = Student::findOrFail($studentId);

        $logs = ActivityLog::where(function($q) use ($studentId) {
            $q->where('subject_type', 'App\\Models\\Student')
              ->where('subject_id', $studentId)
              ->orWhere(function($subq) use ($studentId) {
                  $subq->where('causer_type', 'App\\Models\\Student')
                       ->where('causer_id', $studentId);
              })
              ->orWhere(function($docq) use ($studentId) {
                  // Include document activities for this student
                  $docq->where('subject_type', 'App\\Models\\StudentDocument')
                       ->whereHas('subject', function($sq) use ($studentId) {
                           $sq->where('student_id', $studentId);
                       });
              });
        })
        ->with(['causer'])
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        return response()->json($logs);
    }
}
