<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

/**
 * Daily Report Controller
 *
 * Handles HTTP requests for the Daily Reporting System
 * in the Office Management module.
 */
class DailyReportController extends Controller
{
    protected $reportService;

    public function __construct(DailyReportService $reportService)
    {
        $this->reportService = $reportService;

        // Apply authorization middleware
        $this->middleware('auth');
    }

    /**
     * Display a listing of reports
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!Gate::allows('viewAny', DailyReport::class)) {
            abort(403, 'Unauthorized access to daily reports');
        }

        // Prepare filters
        $filters = [
            'department' => $request->input('department'),
            'status' => $request->input('status'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // Get reports based on user role
        if (auth()->user()->hasRole('Super Admin')) {
            // Super Admin sees all reports
            $reports = $this->reportService->getReports($filters);
        } else {
            // Everyone else (including regular Admin) sees only their own reports
            $reports = $this->reportService->getMyReports(auth()->user(), $filters);
        }

        // Get statistics
        $statistics = $this->reportService->getStatistics($filters);

        return view('office.daily-reports.index', compact('reports', 'statistics', 'filters'));
    }

    /**
     * Show the form for creating a new report
     */
    public function create()
    {
        // Check authorization
        if (!Gate::allows('create', DailyReport::class)) {
            abort(403, 'Unauthorized to create daily reports');
        }

        return view('office.daily-reports.create');
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        // Check authorization
        if (!Gate::allows('create', DailyReport::class)) {
            abort(403, 'Unauthorized to create daily reports');
        }

        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'report_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:in_progress,review,completed',
        ]);

        try {
            $report = $this->reportService->createReport($validated, auth()->user());

            return redirect()
                ->route('office.daily-reports.index')
                ->with('success', 'Daily report submitted successfully!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create report: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified report
     */
    public function show(DailyReport $dailyReport)
    {
        // Check authorization
        if (!Gate::allows('view', $dailyReport)) {
            abort(403, 'Unauthorized to view this report');
        }

        $dailyReport->load(['submittedBy', 'reviewedBy']);

        return view('office.daily-reports.show', compact('dailyReport'));
    }

    /**
     * Show the form for editing the specified report
     */
    public function edit(DailyReport $dailyReport)
    {
        // Check authorization
        if (!Gate::allows('update', $dailyReport)) {
            abort(403, 'Unauthorized to edit this report');
        }

        return view('office.daily-reports.edit', compact('dailyReport'));
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, DailyReport $dailyReport)
    {
        // Check authorization
        if (!Gate::allows('update', $dailyReport)) {
            abort(403, 'Unauthorized to update this report');
        }

        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'report_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:in_progress,review,completed',
        ]);

        try {
            $this->reportService->updateReport($dailyReport, $validated);

            return redirect()
                ->route('office.daily-reports.index')
                ->with('success', 'Report updated successfully!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update report: ' . $e->getMessage());
        }
    }

    /**
     * Review a report (admin action)
     */
    public function review(Request $request, DailyReport $dailyReport)
    {
        // Check authorization
        if (!Gate::allows('review', $dailyReport)) {
            abort(403, 'Unauthorized to review this report');
        }

        // Validate request
        $validated = $request->validate([
            'review_comment' => 'nullable|string|max:1000',
        ]);

        try {
            $this->reportService->reviewReport(
                $dailyReport,
                auth()->user(),
                $validated['review_comment'] ?? null
            );

            return back()->with('success', 'Report reviewed successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to review report: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified report
     */
    public function destroy(DailyReport $dailyReport)
    {
        // Check authorization
        if (!Gate::allows('delete', $dailyReport)) {
            abort(403, 'Unauthorized to delete this report');
        }

        try {
            $this->reportService->deleteReport($dailyReport);

            return redirect()
                ->route('office.daily-reports.index')
                ->with('success', 'Report deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete report: ' . $e->getMessage());
        }
    }
}
