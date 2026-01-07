<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Notifications\StudentApprovedNotification;
use App\Notifications\StudentRejectedNotification;
use App\Notifications\NewStudentRegisteredNotification;
use App\Notifications\StudentAssignedNotification;
use App\Services\ActivityLogService;
use App\Services\ChecklistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    protected $activityLog;
    protected $checklistService;

    public function __construct(ActivityLogService $activityLog, ChecklistService $checklistService)
    {
        $this->activityLog = $activityLog;
        $this->checklistService = $checklistService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $query = Student::with(['assignedUser', 'creator', 'activeProfilePhoto', 'targetUniversity', 'targetProgram']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by account status
        if ($request->has('account_status') && $request->account_status != '') {
            $query->where('account_status', $request->account_status);
        }

        // Filter by assigned user
        if ($request->has('assigned_to') && $request->assigned_to != '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by university
        if ($request->has('university_id') && $request->university_id != '') {
            $query->where('target_university_id', $request->university_id);
        }

        // Filter by program
        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('target_program_id', $request->program_id);
        }

        // If employee, show only assigned students
        if (Auth::user()->isEmployee() && !Auth::user()->isAdmin()) {
            $query->where('assigned_to', Auth::id());
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Handle CSV export
        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportToCSV($query->get());
        }

        // Get per_page value from request, default to 25
        $perPage = $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        // Sort students by creation date (latest first)
        $students = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(request()->except('page'));

        // Get employees for filter dropdown (for admins)
        $employees = collect();
        if (Auth::user()->hasRole(['Super Admin', 'Admin'])) {
            $employees = User::role(['Employee', 'Admin', 'Super Admin'])
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        // Get universities and programs for filters
        $universities = \App\Models\University::active()->orderBy('name')->get();
        $programs = \App\Models\Program::active()->orderBy('name')->get();

        return view('students.index', compact('students', 'employees', 'universities', 'programs'));
    }

    /**
     * Display assigned students for the logged-in employee.
     */
    public function myStudents(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        // Get only students assigned to the logged-in user
        $query = Student::with(['assignedUser', 'creator', 'activeProfilePhoto', 'targetUniversity', 'targetProgram'])
            ->where('assigned_to', Auth::id());

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by account status
        if ($request->has('account_status') && $request->account_status != '') {
            $query->where('account_status', $request->account_status);
        }

        // Filter by university
        if ($request->has('university_id') && $request->university_id != '') {
            $query->where('target_university_id', $request->university_id);
        }

        // Filter by program
        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('target_program_id', $request->program_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Handle CSV export
        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportToCSV($query->get());
        }

        // Get per_page value from request, default to 25
        $perPage = $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        // Sort students by creation date (latest first)
        $students = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(request()->except('page'));

        // Get universities and programs for filters
        $universities = \App\Models\University::active()->orderBy('name')->get();
        $programs = \App\Models\Program::active()->orderBy('name')->get();

        return view('students.my-students', compact('students', 'universities', 'programs'));
    }

    /**
     * Export students to CSV
     */
    private function exportToCSV($students)
    {
        $filename = 'students_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['#', 'Name', 'Email', 'Phone', 'University', 'Program', 'Status', 'Account Status', 'Created Date']);

            // Add data rows
            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->name,
                    $student->email,
                    $student->phone,
                    $student->targetUniversity->name ?? 'N/A',
                    $student->targetProgram->name ?? 'N/A',
                    ucfirst($student->status),
                    ucfirst($student->account_status),
                    $student->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $this->authorize('create', Student::class);

        $users = User::role(['Admin', 'Employee', 'Super Admin'])
            ->where('status', 'active')
            ->get();

        $universities = \App\Models\University::active()->ordered()->get();

        return view('students.create', compact('users', 'universities'));
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request)
    {
        DB::beginTransaction();

        try {
            $student = Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'target_university_id' => $request->target_university_id,
                'target_program_id' => $request->target_program_id,
                'course' => $request->course,
                'status' => $request->status ?? 'new',
                'account_status' => 'pending',
                'assigned_to' => Auth::id(),
                'created_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            // Initialize checklists for the student
            $this->checklistService->initializeChecklistsForStudent($student);

            // Log activity
            $this->activityLog->logStudentCreated($student);

            // Send notification to all admins about new student registration
            try {
                $admins = User::role(['Super Admin', 'Admin'])->where('status', 'active')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new NewStudentRegisteredNotification($student));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send new student notification to admins: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()
                ->route('students.show', $student)
                ->with('success', 'Student created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $this->authorize('view', $student);

        // Optimize eager loading for production - load relationships efficiently
        $student->load([
            'user',
            'assignedUser',
            'creator',
            'targetUniversity',
            'targetProgram',
            'followUps' => function($query) {
                $query->orderBy('created_at', 'desc')->with('creator');
            },
            'checklists' => function($query) {
                $query->with([
                    'checklistItem',
                    'reviewer',
                    'documents' => function($docQuery) {
                        $docQuery->with(['uploader', 'reviewer']);
                    }
                ]);
            },
            'documents' => function($query) {
                $query->with(['checklistItem', 'uploader', 'reviewer']);
            }
        ]);

        $checklistProgress = $this->checklistService->getChecklistProgress($student);

        return view('students.show', compact('student', 'checklistProgress'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $this->authorize('update', $student);

        $users = User::role(['Admin', 'Employee', 'Super Admin'])
            ->where('status', 'active')
            ->get();

        $universities = \App\Models\University::active()->ordered()->get();

        return view('students.edit', compact('student', 'users', 'universities'));
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        DB::beginTransaction();

        try {
            $oldAssignedTo = $student->assigned_to;
            $oldProgramId = $student->target_program_id;

            $student->update($request->only([
                'name',
                'email',
                'phone',
                'country',
                'target_university_id',
                'target_program_id',
                'country',
                'course',
                'status',
                'notes',
            ]));

            // Only Admin/Super Admin can reassign
            if (Auth::user()->isAdmin() && $request->has('assigned_to')) {
                $student->assigned_to = $request->assigned_to;
                $student->save();

                if ($oldAssignedTo != $request->assigned_to) {
                    $this->activityLog->logStudentAssigned($student, $oldAssignedTo, $request->assigned_to);

                    // Send notification to newly assigned counselor
                    try {
                        $newCounselor = User::find($request->assigned_to);
                        if ($newCounselor) {
                            $newCounselor->notify(new StudentAssignedNotification($student, Auth::user()));
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to send assignment notification: ' . $e->getMessage());
                    }
                }
            }

            // If program has changed, reinitialize checklists
            if ($oldProgramId != $request->target_program_id) {
                $this->checklistService->reinitializeChecklistsForProgramChange($student);

                $oldProgramName = $oldProgramId ? \App\Models\Program::find($oldProgramId)?->name ?? 'Unknown' : 'None';
                $newProgramName = $request->target_program_id ? \App\Models\Program::find($request->target_program_id)?->name ?? 'Unknown' : 'None';

                $this->activityLog->log(
                    'student',
                    "Program changed from {$oldProgramName} to {$newProgramName}",
                    $student,
                    [
                        'old_program_id' => $oldProgramId,
                        'new_program_id' => $request->target_program_id,
                        'old_program_name' => $oldProgramName,
                        'new_program_name' => $newProgramName,
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('students.show', $student)
                ->with('success', 'Student updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    /**
     * Show approve form with university, program, and counselor assignment
     */
    public function showApproveForm(Student $student)
    {
        $this->authorize('approve', $student);

        // Get all active universities and programs
        $universities = \App\Models\University::active()->ordered()->get();
        $programs = \App\Models\Program::active()->get();

        // Get all employees (Super Admin, Admin, Employee)
        $counselors = \App\Models\User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Super Admin', 'Admin', 'Employee']);
        })->where('status', 'active')
          ->orderBy('name')
          ->get();

        return view('students.approve', compact('student', 'universities', 'programs', 'counselors'));
    }

    /**
     * Approve student account with university, program, and counselor assignment.
     */
    public function approve(Request $request, Student $student)
    {
        $this->authorize('approve', $student);

        $request->validate([
            'target_university_id' => 'required|exists:universities,id',
            'target_program_id' => 'required|exists:programs,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            // Assign university, program, and counselor
            $student->target_university_id = $request->target_university_id;
            $student->target_program_id = $request->target_program_id;
            $student->assigned_to = $request->assigned_to;

            // Create user account if not exists
            if (!$student->user_id) {
                // Check if a user with this email already exists
                $existingUser = User::where('email', $student->email)->first();

                if ($existingUser) {
                    // Link to existing user if they're a student
                    if ($existingUser->hasRole('Student')) {
                        $student->user_id = $existingUser->id;
                    } else {
                        DB::rollBack();
                        return back()->withInput()->with('error', 'A user account with this email already exists. Please use a different email address for the student.');
                    }
                } else {
                    // Generate a password if student doesn't have one
                    $password = $student->password ?? Hash::make(Str::random(16));

                    // Create new user account
                    $user = User::create([
                        'name' => $student->name,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'password' => $password, // Use existing hashed password or generate new one
                        'status' => 'active',
                        'email_verified_at' => now(),
                    ]);

                    // Assign Student role
                    $user->assignRole('Student');

                    // Link user to student record
                    $student->user_id = $user->id;

                    // Send welcome email notification
                    try {
                        $user->notify(new StudentApprovedNotification($student));
                    } catch (\Exception $e) {
                        // Log notification error but don't fail the approval
                        Log::error('Failed to send approval notification: ' . $e->getMessage());
                    }
                }
            }

            $student->account_status = 'approved';
            $student->save();

            // Assign checklist items based on the selected program
            $this->checklistService->initializeChecklistsForStudent($student);

            $this->activityLog->logStudentApproved($student);

            // Send notifications
            try {
                // 1. Notify the student about approval
                if ($student->user) {
                    $student->user->notify(new StudentApprovedNotification($student));
                }

                // 2. Notify the assigned counselor
                $assignedCounselor = User::find($request->assigned_to);
                if ($assignedCounselor) {
                    $assignedCounselor->notify(new StudentAssignedNotification($student, Auth::user()));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send approval notifications: ' . $e->getMessage());
            }

            DB::commit();

            // Fetch related models for success message
            $counselor = User::find($request->assigned_to);
            $university = \App\Models\University::find($request->target_university_id);
            $program = \App\Models\Program::find($request->target_program_id);

            if (!$counselor || !$university || !$program) {
                Log::warning('Missing related models after approval', [
                    'counselor' => $counselor ? 'found' : 'missing',
                    'university' => $university ? 'found' : 'missing',
                    'program' => $program ? 'found' : 'missing',
                ]);
                return redirect()->route('students.show', $student)
                    ->with('success', 'Student account approved successfully! A welcome email has been sent.');
            }

            return redirect()->route('students.show', $student)
                ->with('success', "Student account approved successfully! Enrolled in {$program->name} at {$university->name} and assigned to {$counselor->name}. A welcome email has been sent.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student approval failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to approve student: ' . $e->getMessage());
        }
    }

    /**
     * Reject student account.
     */
    public function reject(Request $request, Student $student)
    {
        $this->authorize('approve', $student);

        $student->account_status = 'rejected';
        $student->save();

        $this->activityLog->logStudentRejected($student, $request->reason ?? '');

        // Send rejection notification to student
        try {
            if ($student->user) {
                $student->user->notify(new StudentRejectedNotification($student, $request->reason ?? ''));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send rejection notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Student account rejected and notification email sent.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully!');
    }
}
