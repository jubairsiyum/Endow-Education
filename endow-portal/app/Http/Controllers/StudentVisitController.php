<?php

namespace App\Http\Controllers;

use App\Models\StudentVisit;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentVisitController extends Controller
{
    protected $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
        $this->middleware('auth');
    }

    /**
     * Display a listing of student visits.
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', StudentVisit::class);

            $query = StudentVisit::query();

            // Only select columns that definitely exist to avoid any column errors
            try {
                $query->with('employee');
            } catch (\Exception $e) {
                \Log::warning('Could not eager load employee relationship: ' . $e->getMessage());
            }

            // If employee role, show only their own visits
            if (Auth::user()->isEmployee() && !Auth::user()->isAdmin()) {
                $query->where('employee_id', Auth::id());
            }

            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('student_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by employee (for admins)
            if ($request->has('employee_id') && $request->employee_id != '') {
                $query->where('employee_id', $request->employee_id);
            }

        // Filter by prospective status
        if ($request->has('prospective_status') && $request->prospective_status != '') {
            $query->where('prospective_status', $request->prospective_status);
        }

            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $visits = $query->latest()->paginate(15);

            // Get employees for filter dropdown (for admins)
            $employees = collect();
            if (Auth::user()->isAdmin()) {
                try {
                    $employees = User::role(['Employee', 'Admin', 'Super Admin'])
                        ->orderBy('name')
                        ->get();
                } catch (\Exception $e) {
                    \Log::warning('Could not load employees: ' . $e->getMessage());
                }
            }

            return view('student-visits.index', compact('visits', 'employees'));
        } catch (\Exception $e) {
            \Log::error('Student Visits Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('home')->with('error', 'Error loading student visits. Please contact support if this persists.');
        }
    }

    /**
     * Show the form for creating a new student visit.
     */
    public function create()
    {
        try {
            $this->authorize('create', StudentVisit::class);

            // Get employees for assignment (for admins)
            $employees = collect();
            if (Auth::user()->isAdmin()) {
                $employees = User::role(['Employee', 'Admin', 'Super Admin'])
                    ->orderBy('name')
                    ->get();
            }

            return view('student-visits.create', compact('employees'));
        } catch (\Exception $e) {
            \Log::error('Student Visit Create Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created student visit in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', StudentVisit::class);

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'prospective_status' => [
                'required',
                Rule::in(StudentVisit::getStatuses())
            ],
            'employee_id' => [
                'nullable',
                Rule::exists('users', 'id')
            ],
            'notes' => 'nullable|string',
        ]);

        // Ensure a default status to avoid null writes
        $validated['prospective_status'] = $validated['prospective_status'] ?? StudentVisit::STATUS_PROSPECTIVE_WARM;

        // Set default employee to logged-in user if not provided
        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = Auth::id();
        }

        // Only admins can assign to other employees
        if (!Auth::user()->isAdmin()) {
            $validated['employee_id'] = Auth::id();
        }

        $visit = StudentVisit::create($validated);

        // Log activity
        $this->activityLog->log(
            'student_visit',
            "Created student visit record for {$visit->student_name}",
            $visit,
            ['student_name' => $visit->student_name, 'phone' => $visit->phone]
        );

        return redirect()->route('student-visits.index')
            ->with('success', 'Student visit record created successfully.');
    }

    /**
     * Display the specified student visit.
     */
    public function show(StudentVisit $studentVisit)
    {
        try {
            $this->authorize('view', $studentVisit);

            // Eager load employee relationship
            $studentVisit->load('employee');

            return view('student-visits.show', compact('studentVisit'));
        } catch (\Exception $e) {
            \Log::error('Student Visit Show Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading visit details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified student visit.
     */
    public function edit(StudentVisit $studentVisit)
    {
        try {
            $this->authorize('update', $studentVisit);

            // Get employees for assignment (for admins)
            $employees = collect();
            if (Auth::user()->isAdmin()) {
                $employees = User::role(['Employee', 'Admin', 'Super Admin'])
                    ->orderBy('name')
                    ->get();
            }

            return view('student-visits.edit', compact('studentVisit', 'employees'));
        } catch (\Exception $e) {
            \Log::error('Student Visit Edit Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified student visit in storage.
     */
    public function update(Request $request, StudentVisit $studentVisit)
    {
        $this->authorize('update', $studentVisit);

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'prospective_status' => [
                'required',
                Rule::in(StudentVisit::getStatuses())
            ],
            'employee_id' => [
                'nullable',
                Rule::exists('users', 'id')
            ],
            'notes' => 'nullable|string',
        ]);

        $validated['prospective_status'] = $validated['prospective_status'] ?? StudentVisit::STATUS_PROSPECTIVE_WARM;

        // Only admins can change employee assignment
        if (!Auth::user()->isAdmin()) {
            unset($validated['employee_id']);
        }

        $studentVisit->update($validated);

        // Log activity
        $this->activityLog->log(
            'student_visit',
            "Updated student visit record for {$studentVisit->student_name}",
            $studentVisit,
            ['student_name' => $studentVisit->student_name, 'phone' => $studentVisit->phone]
        );

        // Preserve pagination page in redirect
        $redirectParams = $request->only(['page']);
        return redirect()->route('student-visits.index', $redirectParams)
            ->with('success', 'Student visit record updated successfully.');
    }

    /**
     * Remove the specified student visit from storage.
     */
    public function destroy(StudentVisit $studentVisit)
    {
        $this->authorize('delete', $studentVisit);

        $studentName = $studentVisit->student_name;
        $visitId = $studentVisit->id;

        // Log activity before deletion
        $this->activityLog->log(
            'student_visit',
            "Deleted student visit record for {$studentName}",
            $studentVisit,
            ['student_name' => $studentName, 'id' => $visitId]
        );

        $studentVisit->delete();

        // Preserve pagination page in redirect
        $redirectParams = request()->only(['page']);
        return redirect()->route('student-visits.index', $redirectParams)
            ->with('success', 'Student visit record deleted successfully.');
    }
}
