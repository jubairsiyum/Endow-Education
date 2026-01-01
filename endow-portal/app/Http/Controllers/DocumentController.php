<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of all documents (for admin/employee)
     */
    public function index()
    {
        $user = Auth::user();

        // Build query based on role
        $query = StudentDocument::with(['student.user']);

        // Employees only see documents for their assigned students
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        $documents = $query->latest()->paginate(15);

        return view('documents.index', compact('documents'));
    }

    /**
     * Display documents for a specific student
     */
    public function studentDocuments(Student $student)
    {
        $this->authorize('view', $student);

        $documents = $student->documents()->latest()->get();

        return view('students.documents', compact('student', 'documents'));
    }

    /**
     * Upload a new document
     */
    public function upload(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'student_checklist_id' => 'required|exists:student_checklists,id',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($request->student_id);
        $this->authorize('update', $student);

        DB::beginTransaction();

        try {
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileContent = file_get_contents($file->getRealPath());
                $base64Content = base64_encode($fileContent);

                $document = StudentDocument::create([
                    'student_id' => $student->id,
                    'checklist_item_id' => $request->checklist_item_id,
                    'student_checklist_id' => $request->student_checklist_id,
                    'document_type' => 'student_document',
                    'filename' => $file->getClientOriginalName(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'file_data' => $base64Content,
                    'uploaded_by' => Auth::id(),
                    'status' => 'pending',
                    'notes' => $request->notes,
                ]);

                // Update checklist status to submitted
                $checklist = StudentChecklist::find($request->student_checklist_id);
                if ($checklist && $checklist->status === 'pending') {
                    $checklist->update([
                        'status' => 'submitted',
                        'submitted_at' => now(),
                    ]);
                }

                DB::commit();

                return redirect()->back()->with('success', 'Document uploaded successfully and is pending review.');
            }

            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to upload document.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Download a document
     */
    public function download(StudentDocument $document)
    {
        $this->authorize('view', $document->student);

        $fileContent = base64_decode($document->file_data);

        return response($fileContent)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $document->filename . '"');
    }

    /**
     * View a document in browser
     */
    public function view(StudentDocument $document)
    {
        $this->authorize('view', $document->student);

        $fileContent = base64_decode($document->file_data);

        return response($fileContent)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $document->filename . '"');
    }

    /**
     * Approve a document
     */
    public function approve(StudentDocument $document)
    {
        $this->authorize('update', $document->student);

        $document->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Document approved successfully.');
    }

    /**
     * Reject a document
     */
    public function reject(Request $request, StudentDocument $document)
    {
        $this->authorize('update', $document->student);

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'notes' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Document rejected.');
    }

    /**
     * Delete a document
     */
    public function destroy(StudentDocument $document)
    {
        $this->authorize('delete', $document->student);

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
