<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\WorkAssignment;
use App\Services\WorkAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

/**
 * Work Assignment Controller
 *
 * Handles HTTP requests for the Work Assignment module
 * in the Office Management System.
 */
class WorkAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(WorkAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;

        // Apply authentication middleware
        $this->middleware('auth');
    }

    /**
     * Display a listing of work assignments
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!Gate::allows('viewAny', WorkAssignment::class)) {
            abort(403, 'Unauthorized access to work assignments');
        }

        $user = auth()->user();

        // Redirect admins who are not department managers to My Assignments
        if ($user->hasRole('Admin') && !$user->hasRole('department_manager') && !$user->isManagerOfAnyDepartment()) {
            return redirect()->route('office.work-assignments.my-assignments')
                ->with('info', 'As an admin without department management responsibilities, you can view your assigned tasks here.');
        }

        // Prepare filters
        $filters = [
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'department' => $request->input('department'),
            'assigned_to' => $request->input('assigned_to'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'overdue' => $request->input('overdue'),
        ];

        // Get assignments based on user role
        $assignments = $this->assignmentService->getAssignments($user, $filters);

        // Get statistics
        $statistics = $this->assignmentService->getStatistics($user, $filters);

        // Get available departments for filter
        $departments = $this->assignmentService->getAvailableDepartments($user);

        return view('office.work-assignments.index', compact('assignments', 'statistics', 'filters', 'departments'));
    }

    /**
     * Display assignments assigned to the current user
     */
    public function myAssignments(Request $request)
    {
        $user = auth()->user();

        // Prepare filters
        $filters = [
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
        ];

        // Get user's assignments
        $assignments = $this->assignmentService->getMyAssignments($user, $filters);

        // Get statistics
        $statistics = $this->assignmentService->getStatistics($user, $filters);

        return view('office.work-assignments.my-assignments', compact('assignments', 'statistics', 'filters'));
    }

    /**
     * Show the form for creating a new work assignment
     */
    public function create()
    {
        // Check authorization
        if (!Gate::allows('create', WorkAssignment::class)) {
            abort(403, 'Unauthorized to create work assignments');
        }

        $user = auth()->user();

        // Get available employees and departments
        $employees = $this->assignmentService->getAvailableEmployees($user);
        $departments = $this->assignmentService->getAvailableDepartments($user);

        return view('office.work-assignments.create', compact('employees', 'departments'));
    }

    /**
     * Store a newly created work assignment
     */
    public function store(Request $request)
    {
        // Check authorization
        if (!Gate::allows('create', WorkAssignment::class)) {
            abort(403, 'Unauthorized to create work assignments. Only Super Admins and Department Managers can assign tasks.');
        }

        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'required|exists:users,id',
            'assigned_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:assigned_date',
        ]);

        $user = auth()->user();

        // Additional validation: Department managers can only assign to their departments
        if (!$user->hasAnyRole(['Super Admin']) && $validated['department_id']) {
            $managedDeptIds = $user->managedDepartments->pluck('id')->toArray();
            if (!in_array($validated['department_id'], $managedDeptIds)) {
                return back()
                    ->withInput()
                    ->with('error', 'You can only assign tasks to your own department(s).');
            }
        }

        try {
            $assignment = $this->assignmentService->createAssignment($validated, $user);

            return redirect()
                ->route('office.work-assignments.index')
                ->with('success', 'Work assignment created successfully!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create assignment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified work assignment
     */
    public function show(WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('view', $workAssignment)) {
            abort(403, 'Unauthorized to view this assignment');
        }

        $workAssignment->load(['assignedTo', 'assignedBy', 'department', 'dailyReport']);

        return view('office.work-assignments.show', compact('workAssignment'));
    }

    /**
     * Show the form for editing the specified work assignment
     */
    public function edit(WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('update', $workAssignment)) {
            abort(403, 'Unauthorized to edit this assignment');
        }

        $user = auth()->user();

        // Get available employees and departments
        $employees = $this->assignmentService->getAvailableEmployees($user);
        $departments = $this->assignmentService->getAvailableDepartments($user);

        return view('office.work-assignments.edit', compact('workAssignment', 'employees', 'departments'));
    }

    /**
     * Update the specified work assignment
     */
    public function update(Request $request, WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('update', $workAssignment)) {
            abort(403, 'Unauthorized to update this assignment');
        }

        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        try {
            $assignment = $this->assignmentService->updateAssignment($workAssignment, $validated);

            return redirect()
                ->route('office.work-assignments.show', $assignment)
                ->with('success', 'Work assignment updated successfully!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update assignment: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of a work assignment
     */
    public function updateStatus(Request $request, WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('updateStatus', $workAssignment)) {
            abort(403, 'Unauthorized to update assignment status');
        }

        // Validate request
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $assignment = $this->assignmentService->updateStatus(
                $workAssignment,
                $validated['status'],
                $validated['notes'] ?? null
            );

            return back()->with('success', 'Assignment status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Add employee notes to assignment
     */
    public function addNotes(Request $request, WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('addNotes', $workAssignment)) {
            abort(403, 'Unauthorized to add notes to this assignment');
        }

        // Validate request
        $validated = $request->validate([
            'employee_notes' => 'required|string|max:2000',
        ]);

        try {
            $this->assignmentService->addEmployeeNotes($workAssignment, $validated['employee_notes']);

            return back()->with('success', 'Notes added successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to add notes: ' . $e->getMessage());
        }
    }

    /**
     * Add manager feedback to assignment
     */
    public function addFeedback(Request $request, WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('update', $workAssignment)) {
            abort(403, 'Unauthorized to add feedback to this assignment');
        }

        // Validate request
        $validated = $request->validate([
            'manager_feedback' => 'required|string|max:2000',
        ]);

        try {
            $this->assignmentService->addManagerFeedback($workAssignment, $validated['manager_feedback']);

            return back()->with('success', 'Feedback added successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to add feedback: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified work assignment
     */
    public function destroy(WorkAssignment $workAssignment)
    {
        // Check authorization
        if (!Gate::allows('delete', $workAssignment)) {
            abort(403, 'Unauthorized to delete this assignment');
        }

        try {
            $this->assignmentService->deleteAssignment($workAssignment);

            return redirect()
                ->route('office.work-assignments.index')
                ->with('success', 'Work assignment deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete assignment: ' . $e->getMessage());
        }
    }
}
