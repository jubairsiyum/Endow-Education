<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentChecklist;
use App\Models\ChecklistItem;
use App\Models\StudentDocument;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentChecklistController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display the student dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['targetUniversity', 'targetProgram', 'assignedUser'])
            ->firstOrFail();

        // Calculate checklist progress
        $totalCount = 0;
        $completedCount = 0;
        $pendingCount = 0;

        if ($student->target_program_id) {
            $checklistItems = ChecklistItem::active()
                ->whereHas('programs', function($query) use ($student) {
                    $query->where('programs.id', $student->target_program_id);
                })
                ->get();

            $totalCount = $checklistItems->count();

            foreach ($checklistItems as $item) {
                $studentChecklist = StudentChecklist::where('student_id', $student->id)
                    ->where('checklist_item_id', $item->id)
                    ->first();

                if ($studentChecklist && $studentChecklist->status === 'completed') {
                    $completedCount++;
                } else {
                    $pendingCount++;
                }
            }
        }

        $checklistProgress = [
            'total' => $totalCount,
            'completed' => $completedCount,
            'pending' => $pendingCount,
            'percentage' => $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0,
        ];

        return view('student.dashboard', compact('student', 'checklistProgress'));
    }

    /**
     * Display the student's checklist.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['targetUniversity', 'targetProgram'])
            ->firstOrFail();

        // Log checklist access
        $this->activityLogService->logChecklistAccessed($student);

        // Get checklist items based on target program
        if ($student->target_program_id) {
            $checklistItems = ChecklistItem::active()
                ->whereHas('programs', function($query) use ($student) {
                    $query->where('programs.id', $student->target_program_id);
                })
                ->with(['targetProgram', 'studentChecklists' => function($query) use ($student) {
                    $query->where('student_id', $student->id);
                }])
                ->ordered()
                ->get();
        } else {
            // Show all active checklist items if no program selected
            $checklistItems = ChecklistItem::active()
                ->with(['targetProgram', 'studentChecklists' => function($query) use ($student) {
                    $query->where('student_id', $student->id);
                }])
                ->ordered()
                ->get();
        }

        // Calculate progress
        $totalCount = $checklistItems->count();
        $completedCount = $checklistItems->filter(function($item) use ($student) {
            $studentChecklist = $item->studentChecklists->firstWhere('student_id', $student->id);
            return $studentChecklist && $studentChecklist->status === 'completed';
        })->count();

        return view('student.documents', compact('student', 'checklistItems', 'totalCount', 'completedCount'));
    }

    /**
     * Upload document for a checklist item.
     */
    public function uploadDocument(Request $request, ChecklistItem $checklistItem)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'document' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        // Store the file
        $path = $request->file('document')->store('student-documents/' . $student->id, 'public');

        // Create or update student checklist entry
        $studentChecklist = StudentChecklist::updateOrCreate(
            [
                'student_id' => $student->id,
                'checklist_item_id' => $checklistItem->id,
            ],
            [
                'status' => 'submitted',
                'document_path' => $path,
            ]
        );

        // Log activity
        $this->activityLogService->logActivity(
            $student,
            'document_uploaded',
            "Uploaded document for: {$checklistItem->name}",
            ['checklist_item_id' => $checklistItem->id, 'document_path' => $path]
        );

        return back()->with('success', 'Document uploaded successfully! Your document is now under review.');
    }

    /**
     * Delete uploaded document.
     */
    public function deleteDocument(StudentChecklist $studentChecklist)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if ($studentChecklist->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file from storage
        if ($studentChecklist->document_path) {
            Storage::disk('public')->delete($studentChecklist->document_path);
        }

        // Log activity before deletion
        $this->activityLogService->logActivity(
            $student,
            'document_deleted',
            "Deleted document for: {$studentChecklist->checklistItem->name}",
            ['checklist_item_id' => $studentChecklist->checklist_item_id]
        );

        // Reset status to pending and clear document path
        $studentChecklist->update([
            'status' => 'pending',
            'document_path' => null,
            'feedback' => null,
        ]);

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Show edit profile form.
     */
    public function editProfile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['targetUniversity', 'targetProgram'])
            ->firstOrFail();

        return view('student.profile-edit', compact('student'));
    }

    /**
     * Update student profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date|after:today',
            'highest_qualification' => 'nullable|in:high_school,bachelors,masters,phd',
            'previous_institution' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student->update($request->only([
            'name',
            'email',
            'phone',
            'country',
            'date_of_birth',
            'gender',
            'address',
            'passport_number',
            'passport_expiry_date',
            'highest_qualification',
            'previous_institution',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relationship',
        ]));

        // Also update user name and email
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Log activity
        $this->activityLogService->logActivity(
            $student,
            'profile_updated',
            'Student updated their profile information'
        );

        return redirect()->route('student.dashboard')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show FAQ page.
     */
    public function faq()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        return view('student.faq', compact('student'));
    }

    /**
     * Show emergency contact page.
     */
    public function emergencyContact()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['targetUniversity', 'assignedUser'])
            ->firstOrFail();

        return view('student.emergency-contact', compact('student'));
    }

    /**
     * Submit contact form.
     */
    public function submitContact(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:normal,high,urgent',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Log the contact submission
        $this->activityLogService->logActivity(
            $student,
            'contact_submitted',
            "Student submitted contact form - Subject: {$request->subject}",
            [
                'subject' => $request->subject,
                'priority' => $request->priority,
                'message' => $request->message,
            ]
        );

        // Here you could also send an email notification to admin/counselor
        // Mail::to($student->assignedUser->email)->send(new StudentContactMessage($student, $request->all()));

        return back()->with('success', 'Your message has been submitted successfully! We will get back to you soon.');
    }
}
