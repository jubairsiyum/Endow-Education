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
use App\Notifications\StudentAccountCreatedNotification;
use App\Services\ActivityLogService;
use App\Services\ChecklistService;
use App\Services\StudentCsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    protected $activityLog;
    protected $checklistService;
    protected $csvExportService;

    public function __construct(
        ActivityLogService $activityLog,
        ChecklistService $checklistService,
        StudentCsvExportService $csvExportService
    ) {
        $this->activityLog = $activityLog;
        $this->checklistService = $checklistService;
        $this->csvExportService = $csvExportService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        // Check which columns exist before eager loading relationships
        $eagerLoad = ['assignedUser', 'creator', 'activeProfilePhoto'];
        
        try {
            if (DB::getSchemaBuilder()->hasColumn('students', 'target_university_id')) {
                $eagerLoad[] = 'targetUniversity';
            }
            if (DB::getSchemaBuilder()->hasColumn('students', 'target_program_id')) {
                $eagerLoad[] = 'targetProgram';
            }
        } catch (\Exception $e) {
            Log::error('Error checking student columns: ' . $e->getMessage());
        }

        $query = Student::with($eagerLoad);

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
            try {
                if (DB::getSchemaBuilder()->hasColumn('students', 'target_university_id')) {
                    $query->where('target_university_id', $request->university_id);
                }
            } catch (\Exception $e) {
                Log::error('Error filtering by university: ' . $e->getMessage());
            }
        }

        // Filter by program
        if ($request->has('program_id') && $request->program_id != '') {
            try {
                if (DB::getSchemaBuilder()->hasColumn('students', 'target_program_id')) {
                    $query->where('target_program_id', $request->program_id);
                }
            } catch (\Exception $e) {
                Log::error('Error filtering by program: ' . $e->getMessage());
            }
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
        try {
            if (DB::getSchemaBuilder()->hasTable('universities')) {
                $universities = \App\Models\University::active()->orderBy('name')->get();
            } else {
                $universities = collect();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching universities: ' . $e->getMessage());
            $universities = collect();
        }

        try {
            if (DB::getSchemaBuilder()->hasTable('programs')) {
                $programs = \App\Models\Program::active()->orderBy('name')->get();
            } else {
                $programs = collect();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching programs: ' . $e->getMessage());
            $programs = collect();
        }

        return view('students.index', compact('students', 'employees', 'universities', 'programs'));
    }

    /**
     * Display assigned students for the logged-in employee.
     */
    public function myStudents(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        // Check which columns exist before eager loading relationships
        $eagerLoad = ['assignedUser', 'creator', 'activeProfilePhoto'];
        
        try {
            if (DB::getSchemaBuilder()->hasColumn('students', 'target_university_id')) {
                $eagerLoad[] = 'targetUniversity';
            }
            if (DB::getSchemaBuilder()->hasColumn('students', 'target_program_id')) {
                $eagerLoad[] = 'targetProgram';
            }
        } catch (\Exception $e) {
            Log::error('Error checking student columns: ' . $e->getMessage());
        }

        // Get only students assigned to the logged-in user
        $query = Student::with($eagerLoad)
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
            try {
                if (DB::getSchemaBuilder()->hasColumn('students', 'target_university_id')) {
                    $query->where('target_university_id', $request->university_id);
                }
            } catch (\Exception $e) {
                Log::error('Error filtering by university: ' . $e->getMessage());
            }
        }

        // Filter by program
        if ($request->has('program_id') && $request->program_id != '') {
            try {
                if (DB::getSchemaBuilder()->hasColumn('students', 'target_program_id')) {
                    $query->where('target_program_id', $request->program_id);
                }
            } catch (\Exception $e) {
                Log::error('Error filtering by program: ' . $e->getMessage());
            }
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
        try {
            if (DB::getSchemaBuilder()->hasTable('universities')) {
                $universities = \App\Models\University::active()->orderBy('name')->get();
            } else {
                $universities = collect();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching universities: ' . $e->getMessage());
            $universities = collect();
        }

        try {
            if (DB::getSchemaBuilder()->hasTable('programs')) {
                $programs = \App\Models\Program::active()->orderBy('name')->get();
            } else {
                $programs = collect();
            }
        } catch (\Exception $e) {
            Log::error('Error fetching programs: ' . $e->getMessage());
            $programs = collect();
        }

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
            fputcsv($file, [
                '#',
                'Name',
                'Email',
                'Phone',
                'Passport',
                'SSC (Year+Result)',
                'HSC (Year+Result)',
                'IELTS',
                'University',
                'Program',
                'Status',
                'Account Status',
                'Created Date'
            ]);

            // Add data rows
            foreach ($students as $index => $student) {
                try {
                    // Format SSC
                    $ssc = 'N/A';
                    if (isset($student->ssc_year) || isset($student->ssc_result)) {
                        $year = $student->ssc_year ?? '';
                        $result = $student->ssc_result ?? '';
                        if ($year || $result) {
                            $ssc = trim($year . ' - ' . $result);
                            $ssc = $ssc === '-' ? 'N/A' : $ssc;
                        }
                    }

                    // Format HSC
                    $hsc = 'N/A';
                    if (isset($student->hsc_year) || isset($student->hsc_result)) {
                        $year = $student->hsc_year ?? '';
                        $result = $student->hsc_result ?? '';
                        if ($year || $result) {
                            $hsc = trim($year . ' - ' . $result);
                            $hsc = $hsc === '-' ? 'N/A' : $hsc;
                        }
                    }

                    // Format IELTS
                    $ielts = 'No';
                    if (isset($student->has_ielts) && $student->has_ielts) {
                        $score = isset($student->ielts_score) && $student->ielts_score ? ' (' . $student->ielts_score . ')' : '';
                        $ielts = 'Yes' . $score;
                    }

                    fputcsv($file, [
                        $index + 1,
                        $student->name ?? '',
                        $student->email ?? '',
                        $student->phone ?? '',
                        $student->passport_number ?? 'N/A',
                        $ssc,
                        $hsc,
                        $ielts,
                        optional($student->targetUniversity)->name ?? 'N/A',
                        optional($student->targetProgram)->name ?? 'N/A',
                        ucfirst($student->status ?? 'new'),
                        ucfirst($student->account_status ?? 'pending'),
                        $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : 'N/A',
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue processing other students
                    Log::error('CSV Export Error for student ID ' . ($student->id ?? 'unknown') . ': ' . $e->getMessage());
                    continue;
                }
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
            // Generate a temporary password (8-12 characters, mix of letters and numbers)
            $temporaryPassword = $this->generateTemporaryPassword();

            // Create student record
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
                'password' => Hash::make($temporaryPassword), // Store hashed password in student table
            ]);

            // Create user account immediately so student can login
            $existingUser = User::where('email', $student->email)->first();

            if ($existingUser) {
                // Check if existing user is a student
                if ($existingUser->hasRole('Student')) {
                    $student->user_id = $existingUser->id;
                    // Update existing user's password
                    $existingUser->update(['password' => Hash::make($temporaryPassword)]);
                    $student->save();
                } else {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'A user account with this email already exists with a different role. Please use a different email address.');
                }
            } else {
                // Create new user account
                $user = User::create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'password' => Hash::make($temporaryPassword),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                // Assign Student role
                $user->assignRole('Student');

                // Link user to student record
                $student->user_id = $user->id;
                $student->save();
            }

            // Initialize checklists for the student
            $this->checklistService->initializeChecklistsForStudent($student);

            // Log activity
            $this->activityLog->logStudentCreated($student);

            // Send temporary password email to the student
            try {
                if ($student->user) {
                    Log::info('Sending account credentials email', [
                        'student_id' => $student->id,
                        'student_email' => $student->email,
                        'temporary_password' => $temporaryPassword,
                        'password_length' => strlen($temporaryPassword)
                    ]);
                    $student->user->notify(new StudentAccountCreatedNotification($student, $temporaryPassword));
                    Log::info('Account credentials email sent successfully to: ' . $student->email);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send account credentials email to student', [
                    'student_email' => $student->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

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
                ->with('success', 'Student created successfully! Login credentials have been sent to ' . $student->email);

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

        try {
            Log::info("Loading student ID: {$student->id}");

            // Load relationships one by one with individual error handling
            $eagerLoad = [];

            // Basic relationships - load safely
            try {
                if ($student->user_id) {
                    $eagerLoad[] = 'user';
                }
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot load user - " . $e->getMessage());
            }

            try {
                if ($student->assigned_to) {
                    $eagerLoad[] = 'assignedUser';
                }
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot load assignedUser - " . $e->getMessage());
            }

            try {
                if ($student->created_by) {
                    $eagerLoad[] = 'creator';
                }
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot load creator - " . $e->getMessage());
            }

            // Load follow-ups with error handling
            try {
                $eagerLoad['followUps'] = function($query) {
                    $query->orderBy('created_at', 'desc')
                          ->with(['creator' => function($q) {
                              $q->select('id', 'name', 'email');
                          }]);
                };
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot setup followUps - " . $e->getMessage());
            }

            // Load checklists with error handling
            try {
                $eagerLoad['checklists'] = function($query) {
                    $query->with([
                        'checklistItem' => function($q) {
                            $q->select('id', 'title', 'description', 'required', 'order');
                        },
                        'reviewer' => function($q) {
                            $q->select('id', 'name', 'email');
                        },
                        'documents' => function($docQuery) {
                            $docQuery->with([
                                'uploader' => function($q) {
                                    $q->select('id', 'name', 'email');
                                },
                                'reviewer' => function($q) {
                                    $q->select('id', 'name', 'email');
                                }
                            ]);
                        }
                    ]);
                };
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot setup checklists - " . $e->getMessage());
            }

            // Load documents with error handling
            try {
                $eagerLoad['documents'] = function($query) {
                    $query->with([
                        'checklistItem' => function($q) {
                            $q->select('id', 'title', 'description');
                        },
                        'uploader' => function($q) {
                            $q->select('id', 'name', 'email');
                        },
                        'reviewer' => function($q) {
                            $q->select('id', 'name', 'email');
                        }
                    ]);
                };
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot setup documents - " . $e->getMessage());
            }

            // Conditionally add university and program relationships
            try {
                if (Schema::hasColumn('students', 'target_university_id') && $student->target_university_id) {
                    $eagerLoad[] = 'targetUniversity';
                }
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot check/load targetUniversity - " . $e->getMessage());
            }

            try {
                if (Schema::hasColumn('students', 'target_program_id') && $student->target_program_id) {
                    $eagerLoad[] = 'targetProgram';
                }
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot check/load targetProgram - " . $e->getMessage());
            }

            // Load payments relationship if it exists
            try {
                if (method_exists($student, 'payments')) {
                    $eagerLoad['payments'] = function($query) {
                        $query->orderBy('payment_date', 'desc')
                              ->with(['creator' => function($q) {
                                  $q->select('id', 'name', 'email');
                              }]);
                    };
                }
            } catch (\Exception $e) {
                Log::warning("Student {$student->id}: Cannot setup payments - " . $e->getMessage());
            }

            // Load all relationships with global error handling
            try {
                if (!empty($eagerLoad)) {
                    $student->load($eagerLoad);
                }
                Log::info("Student {$student->id}: Successfully loaded relationships");
            } catch (\Exception $e) {
                Log::error("Student {$student->id}: Failed to load relationships - " . $e->getMessage());
                Log::error("Trace: " . $e->getTraceAsString());
                // Continue anyway - we can still show basic student info
            }

            // Ensure critical relationships are initialized even if loading failed
            if (!$student->relationLoaded('checklists')) {
                try {
                    $student->load(['checklists' => function($query) {
                        $query->with([
                            'checklistItem' => function($q) {
                                $q->select('id', 'title', 'description', 'required', 'order');
                            },
                            'reviewer' => function($q) {
                                $q->select('id', 'name', 'email');
                            },
                            'documents' => function($docQuery) {
                                $docQuery->with([
                                    'uploader' => function($q) {
                                        $q->select('id', 'name', 'email');
                                    },
                                    'reviewer' => function($q) {
                                        $q->select('id', 'name', 'email');
                                    }
                                ]);
                            }
                        ]);
                    }]);
                    Log::info("Student {$student->id}: Checklists loaded separately");
                } catch (\Exception $e) {
                    Log::error("Student {$student->id}: Cannot load checklists separately - " . $e->getMessage());
                    // Set empty collection to prevent view errors
                    $student->setRelation('checklists', collect());
                }
            }

            if (!$student->relationLoaded('documents')) {
                try {
                    $student->load(['documents' => function($query) {
                        $query->with(['checklistItem', 'uploader', 'reviewer']);
                    }]);
                } catch (\Exception $e) {
                    Log::error("Student {$student->id}: Cannot load documents separately - " . $e->getMessage());
                    $student->setRelation('documents', collect());
                }
            }

            if (!$student->relationLoaded('followUps')) {
                try {
                    $student->load(['followUps' => function($query) {
                        $query->orderBy('created_at', 'desc')->with('creator');
                    }]);
                } catch (\Exception $e) {
                    Log::error("Student {$student->id}: Cannot load followUps separately - " . $e->getMessage());
                    $student->setRelation('followUps', collect());
                }
            }

            // Get checklist progress with error handling
            try {
                $checklistProgress = $this->checklistService->getChecklistProgress($student);
            } catch (\Exception $e) {
                Log::error("Student {$student->id}: Failed to get checklist progress - " . $e->getMessage());
                // Provide default progress
                $checklistProgress = [
                    'total' => 0,
                    'approved' => 0,
                    'submitted' => 0,
                    'rejected' => 0,
                    'pending' => 0,
                    'in_progress' => 0,
                    'percentage' => 0,
                ];
            }

            Log::info("Student {$student->id}: Rendering view with " . $student->checklists->count() . " checklists");
            return view('students.show', compact('student', 'checklistProgress'));

        } catch (\Exception $e) {
            Log::error("Student show CRITICAL error for ID {$student->id}: " . $e->getMessage());
            Log::error("File: {$e->getFile()} Line: {$e->getLine()}");
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Try to get student basic info for error message
            $studentName = $student->name ?? 'Unknown';
            
            return redirect()->route('students.index')
                ->with('error', "Unable to load details for student: {$studentName} (ID: {$student->id}). Error: " . $e->getMessage());
        }
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
                'course',
                'passport_number',
                'passport_expiry_date',
                'ssc_year',
                'ssc_result',
                'hsc_year',
                'hsc_result',
                'has_ielts',
                'ielts_score',
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

    /**
     * Generate a secure temporary password for new students.
     *
     * @return string
     */
    private function generateTemporaryPassword(): string
    {
        // Generate password with: uppercase, lowercase, numbers, and special character
        // Length: 10 characters for good security
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Exclude I, O to avoid confusion
        $lowercase = 'abcdefghijkmnpqrstuvwxyz'; // Exclude l, o to avoid confusion
        $numbers = '23456789'; // Exclude 0, 1 to avoid confusion
        $special = '@#$%';

        // Ensure at least one of each type
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill remaining characters randomly
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < 10; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle to randomize position of required characters
        return str_shuffle($password);
    }

    /**
     * Export students to CSV
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        // Get student IDs from request (for bulk export)
        $studentIds = $request->input('student_ids', []);

        // Get selected columns from request
        $columns = $request->input('columns', []);

        // Perform the export
        return $this->csvExportService->export($studentIds, $columns);
    }

    /**
     * Show export configuration page
     */
    public function exportForm(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        // Get student IDs from request (if bulk selecting)
        $studentIds = $request->input('student_ids', []);

        // Get available columns
        $availableColumns = $this->csvExportService->getAvailableColumns();

        // Get count of students to be exported
        $exportCount = $this->csvExportService->getExportCount($studentIds);

        return view('students.export', compact('availableColumns', 'studentIds', 'exportCount'));
    }
}
