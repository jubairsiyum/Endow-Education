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
     * Display the student's checklist.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Log checklist access
        $this->activityLogService->logChecklistAccessed($student);

        // Get checklist items based on target program or all active items
        if ($student->target_program_id) {
            $checklistItems = ChecklistItem::active()
                ->whereHas('programs', function($query) use ($student) {
                    $query->where('programs.id', $student->target_program_id);
                })
                ->ordered()
                ->get();
        } else {
            // Show all active checklist items if no program selected
            $checklistItems = ChecklistItem::active()->ordered()->get();
        }

        // Get student's checklist progress
        $studentChecklists = StudentChecklist::where('student_id', $student->id)
            ->with('checklistItem')
            ->get()
            ->keyBy('checklist_item_id');

        // Get documents for each checklist item
        $documents = StudentDocument::where('student_id', $student->id)
            ->with('checklistItem')
            ->get()
            ->groupBy('checklist_item_id');

        return view('student.checklist', compact('student', 'checklistItems', 'studentChecklists', 'documents'));
    }

    /**
     * Upload document for a checklist item.
     */
    public function uploadDocument(Request $request, ChecklistItem $checklistItem)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'document' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
            'remarks' => 'nullable|string|max:500',
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
                'remarks' => $request->remarks,
            ]
        );

        // Create document record
        $newDocument = StudentDocument::create([
            'student_id' => $student->id,
            'checklist_item_id' => $checklistItem->id,
            'title' => $checklistItem->title,
            'file_path' => $path,
            'file_type' => $request->file('document')->getClientOriginalExtension(),
            'uploaded_by' => $user->id,
        ]);

        // Log activity
        $this->activityLogService->logDocumentUploaded($newDocument);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete uploaded document.
     */
    public function deleteDocument(StudentDocument $document)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        if ($document->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file from storage
        \Storage::disk('public')->delete($document->file_path);

        // Log activity before deletion
        $this->activityLogService->logDocumentDeleted($document);

        $document->delete();

        // Update checklist status if no more documents
        $remainingDocs = StudentDocument::where('student_id', $student->id)
            ->where('checklist_item_id', $document->checklist_item_id)
            ->count();

        if ($remainingDocs === 0) {
            StudentChecklist::where('student_id', $student->id)
                ->where('checklist_item_id', $document->checklist_item_id)
                ->update(['status' => 'pending']);
        }

        return back()->with('success', 'Document deleted successfully.');
    }
}
