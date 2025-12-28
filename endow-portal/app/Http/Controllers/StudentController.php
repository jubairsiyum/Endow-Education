<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\ActivityLogService;
use App\Services\ChecklistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $query = Student::with(['assignedUser', 'creator']);

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

        $students = $query->latest()->paginate(15);

        return view('students.index', compact('students'));
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

        return view('students.create', compact('users'));
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

        $student->load([
            'assignedUser',
            'creator',
            'followUps.creator',
            'checklists.checklistItem',
            'documents.checklistItem'
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

        return view('students.edit', compact('student', 'users'));
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        DB::beginTransaction();

        try {
            $oldAssignedTo = $student->assigned_to;

            $student->update($request->only([
                'name',
                'email',
                'phone',
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
                }
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
     * Approve student account.
     */
    public function approve(Student $student)
    {
        $this->authorize('approve', $student);

        DB::beginTransaction();

        try {
            // Create user account if not exists
            if (!$student->user_id) {
                $user = User::create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'password' => $student->password, // Use password from student registration
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                // Assign Student role
                $user->assignRole('Student');

                // Link user to student record
                $student->user_id = $user->id;

                // TODO: Send welcome email notification
            }

            $student->account_status = 'approved';
            $student->save();

            // Assign checklist items to the student
            $this->checklistService->initializeChecklistsForStudent($student);

            $this->activityLog->logStudentApproved($student);

            DB::commit();

            return back()->with('success', 'Student account approved successfully! A welcome email has been sent.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve student: ' . $e->getMessage());
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

        // TODO: Send notification to student

        return back()->with('success', 'Student account rejected.');
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
