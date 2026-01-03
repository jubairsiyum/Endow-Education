<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentChecklist;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentController extends Controller
{
    protected $imageProcessingService;

    public function __construct(ImageProcessingService $imageProcessingService)
    {
        $this->middleware('auth');
        $this->imageProcessingService = $imageProcessingService;
    }

    /**
     * Display a listing of all documents (for admin/employee)
     */
    public function index()
    {
        $user = Auth::user();

        // Build query based on role
        $query = StudentDocument::with(['student', 'student.user', 'checklistItem', 'uploader']);

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
                
                // Check if the file is an image and convert to PDF
                $shouldConvert = $this->imageProcessingService->shouldConvertToPdf($file);
                
                if ($shouldConvert) {
                    // Convert image to PDF
                    $pdfData = $this->imageProcessingService->convertImageToPdf($file, $file->getClientOriginalName());
                    
                    $fileName = $pdfData['filename'];
                    $mimeType = $pdfData['mime_type'];
                    $fileSize = $pdfData['size'];
                    $base64Content = $pdfData['content'];
                } else {
                    // Use original file
                    $fileContent = file_get_contents($file->getRealPath());
                    $base64Content = base64_encode($fileContent);
                    $fileName = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();
                }

                // Build document data with only essential fields
                $documentData = [
                    'student_id' => $student->id,
                    'checklist_item_id' => $request->checklist_item_id,
                    'student_checklist_id' => $request->student_checklist_id,
                    'filename' => $fileName,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'file_data' => $base64Content,
                    'uploaded_by' => Auth::id(),
                ];

                // Add optional fields only if column exists
                if (Schema::hasColumn('student_documents', 'document_type')) {
                    $documentData['document_type'] = 'student_document';
                }
                if (Schema::hasColumn('student_documents', 'file_name')) {
                    $documentData['file_name'] = $fileName;
                }
                if (Schema::hasColumn('student_documents', 'original_name')) {
                    $documentData['original_name'] = $shouldConvert ? $file->getClientOriginalName() : $fileName;
                }
                if (Schema::hasColumn('student_documents', 'status')) {
                    $documentData['status'] = 'submitted';
                }
                if (Schema::hasColumn('student_documents', 'notes') && $request->notes) {
                    $documentData['notes'] = $request->notes;
                }

                $document = StudentDocument::create($documentData);

                // Update checklist status to submitted
                $checklist = StudentChecklist::find($request->student_checklist_id);
                if ($checklist && $checklist->status === 'pending') {
                    $checklist->update([
                        'status' => 'submitted',
                        'submitted_at' => now(),
                    ]);
                }

                DB::commit();

                $successMessage = $shouldConvert 
                    ? 'Image uploaded and converted to PDF successfully! Document is pending review.'
                    : 'Document uploaded successfully and is pending review.';

                return redirect()->back()->with('success', $successMessage);
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
    public function download(Student $student = null, StudentDocument $document)
    {
        if ($student) {
            $this->authorize('view', $student);
            if ($document->student_id !== $student->id) {
                abort(404);
            }
        } else {
            $this->authorize('view', $document->student);
        }

        if ($document->file_data) {
            $fileContent = base64_decode($document->file_data);
        } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $fileContent = Storage::disk('public')->get($document->file_path);
        } else {
            abort(404, 'Document file not found.');
        }

        return response($fileContent)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $document->filename . '"');
    }

    /**
     * View a document in browser
     */
    public function view(Student $student = null, StudentDocument $document)
    {
        if ($student) {
            $this->authorize('view', $student);
            if ($document->student_id !== $student->id) {
                abort(404);
            }
        } else {
            $this->authorize('view', $document->student);
        }

        if ($document->file_data) {
            $fileContent = base64_decode($document->file_data);
        } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $fileContent = Storage::disk('public')->get($document->file_path);
        } else {
            abort(404, 'Document file not found.');
        }

        return view('students.documents.view', compact('student', 'document', 'fileContent'));
    }

    /**
     * Get document data for modal viewer (API endpoint)
     */
    public function getData(StudentDocument $document)
    {
        // Authorization check
        $this->authorize('view', $document->student);

        // Get file content
        if ($document->file_data) {
            $base64Content = $document->file_data;
        } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $fileContent = Storage::disk('public')->get($document->file_path);
            $base64Content = base64_encode($fileContent);
        } else {
            return response()->json(['error' => 'Document file not found'], 404);
        }

        return response()->json([
            'id' => $document->id,
            'filename' => $document->filename,
            'mime_type' => $document->mime_type,
            'file_size' => $document->file_size,
            'file_data' => $base64Content,
        ]);
    }

    /**
     * Approve a document
     */
    public function approve(Student $student = null, StudentDocument $document)
    {
        if ($student) {
            $this->authorize('update', $student);
            if ($document->student_id !== $student->id) {
                abort(404);
            }
        } else {
            $this->authorize('update', $document->student);
        }

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
    public function reject(Request $request, Student $student = null, StudentDocument $document)
    {
        if ($student) {
            $this->authorize('update', $student);
            if ($document->student_id !== $student->id) {
                abort(404);
            }
        } else {
            $this->authorize('update', $document->student);
        }

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
    public function destroy(Student $student = null, StudentDocument $document)
    {
        if ($student) {
            $this->authorize('delete', $student);
            if ($document->student_id !== $student->id) {
                abort(404);
            }
        } else {
            $this->authorize('delete', $document->student);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
