<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultantEvaluation;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultantEvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    /**
     * Display all consultant evaluations.
     */
    public function index(Request $request)
    {
        $query = ConsultantEvaluation::with(['student', 'consultant', 'question'])
            ->orderBy('created_at', 'desc');

        // Filter by consultant
        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $evaluations = $query->paginate(20);

        // Get all consultants (employees) for filter
        $consultants = User::role(['Admin', 'Employee'])
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total' => ConsultantEvaluation::count(),
            'this_month' => ConsultantEvaluation::whereMonth('created_at', now()->month)->count(),
            'excellent' => ConsultantEvaluation::where('rating', 'excellent')->count(),
            'below_average' => ConsultantEvaluation::where('rating', 'below_average')->count(),
        ];

        return view('admin.consultant-evaluations.index', compact('evaluations', 'consultants', 'stats'));
    }

    /**
     * Show detailed evaluations for a specific consultant.
     */
    public function show(User $consultant)
    {
        $evaluations = ConsultantEvaluation::with(['student', 'question'])
            ->where('consultant_id', $consultant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Rating breakdown
        $ratingBreakdown = ConsultantEvaluation::where('consultant_id', $consultant->id)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Average scores per question
        $questionStats = ConsultantEvaluation::with('question')
            ->where('consultant_id', $consultant->id)
            ->get()
            ->groupBy('question_id')
            ->map(function ($evaluations) {
                $ratingValues = [
                    'below_average' => 1,
                    'average' => 2,
                    'neutral' => 3,
                    'good' => 4,
                    'excellent' => 5,
                ];

                $sum = $evaluations->sum(function ($eval) use ($ratingValues) {
                    return $ratingValues[$eval->rating] ?? 0;
                });

                return [
                    'question' => $evaluations->first()->question->question,
                    'count' => $evaluations->count(),
                    'average' => round($sum / $evaluations->count(), 2),
                ];
            });

        return view('admin.consultant-evaluations.show', compact('consultant', 'evaluations', 'ratingBreakdown', 'questionStats'));
    }

    /**
     * Export evaluations to CSV.
     */
    public function export(Request $request)
    {
        $query = ConsultantEvaluation::with(['student', 'consultant', 'question']);

        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }

        $evaluations = $query->orderBy('created_at', 'desc')->get();

        $filename = 'consultant_evaluations_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($evaluations) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['Date', 'Student', 'Consultant', 'Question', 'Rating', 'Comment']);

            // Data rows
            foreach ($evaluations as $eval) {
                fputcsv($file, [
                    $eval->created_at->format('Y-m-d H:i:s'),
                    $eval->student->name ?? 'N/A',
                    $eval->consultant->name ?? 'N/A',
                    $eval->question->question ?? 'N/A',
                    $eval->rating_label,
                    $eval->comment ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
