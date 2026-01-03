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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

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
                ->with(['programs', 'studentChecklists' => function($query) use ($student) {
                    $query->where('student_id', $student->id);
                }])
                ->ordered()
                ->get();
        } else {
            // Show all active checklist items if no program selected
            $checklistItems = ChecklistItem::active()
                ->with(['programs', 'studentChecklists' => function($query) use ($student) {
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
            'document' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        // Delete old file if exists
        $existingChecklist = StudentChecklist::where('student_id', $student->id)
            ->where('checklist_item_id', $checklistItem->id)
            ->first();

        if ($existingChecklist && $existingChecklist->document_path) {
            Storage::disk('public')->delete($existingChecklist->document_path);
            // Delete old document records
            StudentDocument::where('student_checklist_id', $existingChecklist->id)->delete();
        }

        // Store the file in file system
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
                'submitted_at' => now(),
            ]
        );

        // Create document record in student_documents table
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileContent = file_get_contents($file->getRealPath());
            $base64Content = base64_encode($fileContent);

            // Build document data with only essential fields to avoid column errors
            $documentData = [
                'student_id' => $student->id,
                'checklist_item_id' => $checklistItem->id,
                'student_checklist_id' => $studentChecklist->id,
                'filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_data' => $base64Content,
                'uploaded_by' => $user->id,
            ];

            // Add optional fields only if column exists (avoid migration errors)
            if (Schema::hasColumn('student_documents', 'document_type')) {
                $documentData['document_type'] = 'student_document';
            }
            if (Schema::hasColumn('student_documents', 'file_name')) {
                $documentData['file_name'] = $file->getClientOriginalName();
            }
            if (Schema::hasColumn('student_documents', 'original_name')) {
                $documentData['original_name'] = $file->getClientOriginalName();
            }
            if (Schema::hasColumn('student_documents', 'file_path')) {
                $documentData['file_path'] = $path;
            }
            if (Schema::hasColumn('student_documents', 'status')) {
                $documentData['status'] = 'submitted';
            }

            StudentDocument::create($documentData);
        }

        // Log activity
        $this->activityLogService->log(
            'student',
            "Uploaded document for: {$checklistItem->title}",
            $student,
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

        // Delete associated document records
        StudentDocument::where('student_checklist_id', $studentChecklist->id)->delete();

        // Log activity before deletion
        $this->activityLogService->log(
            'student',
            "Deleted document for: {$studentChecklist->checklistItem->title}",
            $student,
            ['checklist_item_id' => $studentChecklist->checklist_item_id]
        );

        // Reset status to pending and clear document path
        $studentChecklist->update([
            'status' => 'pending',
            'document_path' => null,
            'feedback' => null,
            'submitted_at' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
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
        $this->activityLogService->log(
            'student',
            'Student updated their profile information',
            $student
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
        $this->activityLogService->log(
            'student',
            "Student submitted contact form - Subject: {$request->subject}",
            $student,
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

    /**
     * Approve a student's submitted document.
     */
    public function approveDocument(StudentChecklist $studentChecklist)
    {
        $user = Auth::user();

        // Authorize - only employees can approve
        $student = $studentChecklist->student;
        if (Gate::denies('update', $student)) {
            abort(403, 'Unauthorized action.');
        }

        // Update checklist status to approved/completed
        $studentChecklist->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'feedback' => null, // Clear any previous feedback
        ]);

        // Update all associated documents to approved status
        StudentDocument::where('student_checklist_id', $studentChecklist->id)
            ->where('status', 'submitted')
            ->update([
                'status' => 'approved',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);

        // Log activity
        $this->activityLogService->log(
            'employee',
            "Approved document: {$studentChecklist->checklistItem->title} for {$student->name}",
            $student,
            ['checklist_item_id' => $studentChecklist->checklist_item_id]
        );

        return back()->with('success', 'Document approved successfully!');
    }

    /**
     * Reject a student's submitted document with feedback.
     */
    public function rejectDocument(Request $request, StudentChecklist $studentChecklist)
    {
        $user = Auth::user();

        // Authorize - only employees can reject
        $student = $studentChecklist->student;
        if (Gate::denies('update', $student)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        // Update checklist status to rejected with feedback
        $studentChecklist->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'feedback' => $request->feedback,
        ]);

        // Update all associated documents to rejected status
        StudentDocument::where('student_checklist_id', $studentChecklist->id)
            ->where('status', 'submitted')
            ->update([
                'status' => 'rejected',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
                'notes' => $request->feedback,
            ]);

        // Log activity
        $this->activityLogService->log(
            'employee',
            "Rejected document: {$studentChecklist->checklistItem->title} for {$student->name}",
            $student,
            [
                'checklist_item_id' => $studentChecklist->checklist_item_id,
                'feedback' => $request->feedback
            ]
        );

        return back()->with('success', 'Document rejected. Feedback has been sent to the student.');
    }

    /**
     * Allow student to resubmit a rejected document.
     */
    public function resubmitDocument(Request $request, StudentChecklist $studentChecklist)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Check authorization
        if ($studentChecklist->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow resubmission if status is rejected
        if ($studentChecklist->status !== 'rejected') {
            return back()->with('error', 'You can only resubmit rejected documents.');
        }

        $request->validate([
            'document' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        // Delete old file if exists
        if ($studentChecklist->document_path) {
            Storage::disk('public')->delete($studentChecklist->document_path);
        }

        // Delete old document records for this checklist
        StudentDocument::where('student_checklist_id', $studentChecklist->id)->delete();

        // Store the new file
        $path = $request->file('document')->store('student-documents/' . $student->id, 'public');

        // Update student checklist entry
        $studentChecklist->update([
            'status' => 'submitted',
            'document_path' => $path,
            'submitted_at' => now(),
            'feedback' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        // Create new document record
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileContent = file_get_contents($file->getRealPath());
            $base64Content = base64_encode($fileContent);

            // Build document data with only essential fields
            $documentData = [
                'student_id' => $student->id,
                'checklist_item_id' => $studentChecklist->checklist_item_id,
                'student_checklist_id' => $studentChecklist->id,
                'filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_data' => $base64Content,
                'uploaded_by' => $user->id,
            ];

            // Add optional fields only if column exists
            if (Schema::hasColumn('student_documents', 'document_type')) {
                $documentData['document_type'] = 'student_document';
            }
            if (Schema::hasColumn('student_documents', 'file_name')) {
                $documentData['file_name'] = $file->getClientOriginalName();
            }
            if (Schema::hasColumn('student_documents', 'original_name')) {
                $documentData['original_name'] = $file->getClientOriginalName();
            }
            if (Schema::hasColumn('student_documents', 'file_path')) {
                $documentData['file_path'] = $path;
            }
            if (Schema::hasColumn('student_documents', 'status')) {
                $documentData['status'] = 'submitted';
            }

            StudentDocument::create($documentData);
        }

        // Log activity
        $this->activityLogService->log(
            'student',
            "Resubmitted document for: {$studentChecklist->checklistItem->title}",
            $student,
            ['checklist_item_id' => $studentChecklist->checklist_item_id, 'document_path' => $path]
        );

        return back()->with('success', 'Document resubmitted successfully! Your document is now under review.');
    }
}
