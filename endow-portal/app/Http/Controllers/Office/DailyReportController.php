<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $user = auth()->user();

        // Get reports based on user role
        if ($user->hasRole('Super Admin')) {
            // Super Admin sees all reports
            $reports = $this->reportService->getReports($filters);
        } elseif ($user->hasRole('department_manager') || $user->isManagerOfAnyDepartment()) {
            // Department managers see reports from all their managed departments
            $reports = $this->reportService->getManagerReports($user, $filters);
        } else {
            // Everyone else sees only their own reports
            $reports = $this->reportService->getMyReports($user, $filters);
        }

        // Get role-based statistics
        $statistics = $this->reportService->getStatistics($filters, $user);

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
            'status' => 'required|in:draft,submitted,in_progress,review',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'tags' => 'nullable|string|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt',
        ]);

        try {
            $report = $this->reportService->createReport($validated, auth()->user());

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    // Generate unique filename with microseconds
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('daily-reports/' . $report->id, $fileName, 'public');
                    
                    $this->reportService->addAttachment($report, [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ], auth()->user());
                }
            }

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

        $dailyReport->load(['submittedBy', 'reviewedBy', 'reviews.reviewer']);

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
            'status' => 'required|in:draft,submitted,in_progress,review',
            'tags' => 'nullable|string|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt',
        ]);

        try {
            $this->reportService->updateReport($dailyReport, $validated);

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    // Generate unique filename with microseconds
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('daily-reports/' . $dailyReport->id, $fileName, 'public');
                    
                    $this->reportService->addAttachment($dailyReport, [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ], auth()->user());
                }
            }

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
     * Review a report (manager/supervisor action)
     * 
     * ORGANIZATIONAL REVIEW HIERARCHY:
     * - Reports must be submitted (not drafts) before review
     * - Managers/Supervisors review reports from their team members
     * - Users CANNOT review their own reports (enforced by policy)
     * - Super Admin/Admin can review all reports across departments
     * - Department Managers can review reports within their department
     * - Review can mark report as completed (final) or provide instructions (allows further updates)
     */
    public function review(Request $request, DailyReport $dailyReport)
    {
        // Check authorization (prevents self-review, draft reviews, etc.)
        if (!Gate::allows('review', $dailyReport)) {
            abort(403, 'Unauthorized to review this report. You cannot review your own reports or draft reports.');
        }

        // Validate request
        $validated = $request->validate([
            'mark_as_completed' => 'nullable|boolean',
        ]);

        try {
            $this->reportService->reviewReport(
                $dailyReport,
                auth()->user(),
                null, // No comment needed
                $validated['mark_as_completed'] ?? false
            );

            $message = ($validated['mark_as_completed'] ?? false) 
                ? 'Report marked as completed successfully!' 
                : 'Review instruction added successfully!';

            return back()->with('success', $message);
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

    /**
     * Submit report for approval
     */
    public function submit(DailyReport $dailyReport)
    {
        if (!Gate::allows('submit', $dailyReport)) {
            abort(403, 'Unauthorized to submit this report');
        }

        try {
            $this->reportService->submitReport($dailyReport);

            return redirect()
                ->route('office.daily-reports.show', $dailyReport)
                ->with('success', 'Report submitted for approval successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to submit report: ' . $e->getMessage());
        }
    }

    /**
     * Approve a report
     */
    public function approve(Request $request, DailyReport $dailyReport)
    {
        if (!Gate::allows('approve', $dailyReport)) {
            abort(403, 'Unauthorized to approve this report');
        }

        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $this->reportService->approveReport($dailyReport, auth()->user(), $validated['comment'] ?? null);

            return back()->with('success', 'Report approved successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to approve report: ' . $e->getMessage());
        }
    }

    /**
     * Reject a report
     */
    public function reject(Request $request, DailyReport $dailyReport)
    {
        if (!Gate::allows('reject', $dailyReport)) {
            abort(403, 'Unauthorized to reject this report');
        }

        $validated = $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $this->reportService->rejectReport($dailyReport, auth()->user(), $validated['comment'] ?? 'Report rejected by reviewer.');

            return back()->with('success', 'Report rejected. Feedback sent to submitter.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to reject report: ' . $e->getMessage());
        }
    }

    /**
     * Add comment to report
     */
    public function addComment(Request $request, DailyReport $dailyReport)
    {
        if (!Gate::allows('addComments', $dailyReport)) {
            abort(403, 'Unauthorized to comment on this report');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'type' => 'nullable|in:feedback,question,approval,rejection,note',
            'is_internal' => 'nullable|boolean',
        ]);

        try {
            $this->reportService->addComment(
                $dailyReport,
                auth()->user(),
                $validated['comment'],
                $validated['type'] ?? 'feedback',
                $validated['is_internal'] ?? false
            );

            return back()->with('success', 'Comment added successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to add comment: ' . $e->getMessage());
        }
    }

    /**
     * Upload attachment
     */
    public function uploadAttachment(Request $request, DailyReport $dailyReport)
    {
        if (!Gate::allows('addAttachments', $dailyReport)) {
            abort(403, 'Unauthorized to upload attachments');
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('daily-reports/' . $dailyReport->id, $fileName, 'public');

            $this->reportService->addAttachment($dailyReport, [
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ], auth()->user());

            return back()->with('success', 'File uploaded successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }
    /**
     * Export reports as PDF (Super Admin only)
     */
    public function exportPDF(Request $request)
    {
        // Only Super Admin can export reports
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized. Only Super Admin can export reports.');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:draft,submitted,pending_review,in_progress,review,approved,rejected,completed,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        try {
            // Get reports based on filters
            $filters = [
                'user_id' => $validated['user_id'] ?? null,
                'department' => $validated['department_id'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'] ?? null,
                'priority' => $validated['priority'] ?? null,
            ];

            $reports = $this->reportService->getReportsForExport($filters);

            // Generate PDF using Laravel facade
            $pdf = Pdf::loadView('office.daily-reports.pdf.export', [
                'reports' => $reports,
                'filters' => $filters,
                'exportDate' => now(),
            ]);

            // Set paper and orientation
            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $filename = 'daily-reports-' . now()->format('Y-m-d-His') . '.pdf';

            // Download PDF
            return $pdf->download($filename);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Show export form (Super Admin only)
     */
    public function showExportForm()
    {
        // Only Super Admin can access export
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized. Only Super Admin can export reports.');
        }

        // Get all users who have submitted reports
        $users = \App\Models\User::whereHas('dailyReports')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get all departments
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('office.daily-reports.export', compact('users', 'departments'));
    }}

