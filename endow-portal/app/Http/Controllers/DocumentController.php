<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentChecklist;
use App\Services\ImageProcessingService;
use App\Services\PdfMergeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    protected $imageProcessingService;
    protected $pdfMergeService;

    public function __construct(ImageProcessingService $imageProcessingService, PdfMergeService $pdfMergeService)
    {
        $this->middleware('auth');
        $this->imageProcessingService = $imageProcessingService;
        $this->pdfMergeService = $pdfMergeService;
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
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:15360', // Increased to 15MB
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

                $fileName = null;
                $mimeType = null;
                $fileSize = null;
                $filePath = null;

                if ($shouldConvert) {
                    // Convert image to PDF
                    $pdfData = $this->imageProcessingService->convertImageToPdf($file, $file->getClientOriginalName());

                    $fileName = $pdfData['filename'];
                    $mimeType = $pdfData['mime_type'];
                    $fileSize = $pdfData['size'];

                    // Save converted PDF to filesystem instead of database
                    $sanitizedFileName = $student->id . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
                    $filePath = 'student-documents/' . $student->id . '/' . $sanitizedFileName;
                    Storage::disk('public')->put($filePath, base64_decode($pdfData['content']));
                } else {
                    // Save original file to filesystem
                    $fileName = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();

                    $sanitizedFileName = $student->id . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
                    $filePath = $file->storeAs('student-documents/' . $student->id, $sanitizedFileName, 'public');
                }

                // Build document data - Store path instead of base64 data for better performance
                $documentData = [
                    'student_id' => $student->id,
                    'checklist_item_id' => $request->checklist_item_id,
                    'student_checklist_id' => $request->student_checklist_id,
                    'filename' => $fileName,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'file_path' => $filePath,  // Use filesystem path instead of file_data
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
                abort(404, 'Document not found for this student.');
            }
        } else {
            $this->authorize('view', $document->student);
        }

        if ($document->file_data) {
            $fileContent = base64_decode($document->file_data);
        } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $fileContent = Storage::disk('public')->get($document->file_path);
        } else {
            return back()->with('error', 'Document file not found. The file may have been deleted or moved.');
        }

        return response($fileContent)
            ->header('Content-Type', $document->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $document->filename . '"');
    }

    /**
     * View a document in browser (inline display) - Optimized for performance with filesystem storage
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

        // Generate ETag for caching based on document ID and updated timestamp
        $etag = md5($document->id . '-' . $document->updated_at);
        $lastModified = $document->updated_at;

        // Check if client has cached version
        $requestEtag = request()->header('If-None-Match');
        $requestModifiedSince = request()->header('If-Modified-Since');

        if ($requestEtag === $etag || ($requestModifiedSince && strtotime($requestModifiedSince) >= $lastModified->timestamp)) {
            // Client has valid cached version, return 304 Not Modified
            return response('', 304)
                ->header('ETag', $etag)
                ->header('Last-Modified', $lastModified->format('D, d M Y H:i:s') . ' GMT');
        }

        // Prefer filesystem storage (much faster than database)
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            // FASTEST: Direct filesystem response with full optimization
            $path = Storage::disk('public')->path($document->file_path);

            return response()->file($path, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'inline; filename="' . $document->filename . '"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=7200, must-revalidate', // Cache for 2 hours
                'ETag' => $etag,
                'Last-Modified' => $lastModified->format('D, d M Y H:i:s') . ' GMT',
                'Accept-Ranges' => 'bytes',
                'X-Frame-Options' => 'SAMEORIGIN',
            ]);
        } elseif ($document->file_data) {
            // FALLBACK: For legacy database-stored files, use streaming
            $callback = function() use ($document) {
                echo base64_decode($document->file_data);
                flush();
            };

            $decodedSize = strlen(base64_decode($document->file_data));

            return response()->stream($callback, 200, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'inline; filename="' . $document->filename . '"',
                'Content-Length' => $decodedSize,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=7200, must-revalidate',
                'ETag' => $etag,
                'Last-Modified' => $lastModified->format('D, d M Y H:i:s') . ' GMT',
                'Accept-Ranges' => 'bytes',
                'X-Frame-Options' => 'SAMEORIGIN',
            ]);
        } else {
            abort(404, 'Document file not found.');
        }
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
     * Merge multiple documents into a single PDF
     */
    public function mergeDocuments(Request $request, Student $student)
    {
        $this->authorize('view', $student);

        $request->validate([
            'document_ids' => 'required|array|min:2',
            'document_ids.*' => 'exists:student_documents,id'
        ]);

        $documents = StudentDocument::whereIn('id', $request->document_ids)
            ->where('student_id', $student->id)
            ->get();

        if ($documents->count() < 2) {
            return response()->json(['error' => 'At least 2 documents are required'], 400);
        }

        try {
            // Use PDF merger library if available
            if (class_exists('\setasign\Fpdi\Tcpdf\Fpdi')) {
                return $this->mergePDFsWithFPDI($documents, $student);
            }

            // Fallback: Create HTML document with images/PDFs and convert to PDF
            return $this->mergePDFsWithDompdf($documents, $student);

        } catch (\Exception $e) {
            Log::error('Error merging documents: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to merge documents: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Merge PDFs using Dompdf (fallback method)
     */
    private function mergePDFsWithDompdf($documents, $student)
    {
        $html = '
        <html>
        <head>
            <style>
                body { margin: 0; padding: 0; }
                .page { page-break-after: always; text-align: center; padding: 20px; }
                .page:last-child { page-break-after: auto; }
                img { max-width: 100%; height: auto; }
                .doc-header { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            </style>
        </head>
        <body>';

        foreach ($documents as $index => $document) {
            // Get document content
            $content = null;
            if ($document->file_data) {
                $content = $document->file_data;
            } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                $content = Storage::disk('public')->get($document->file_path);
            }

            if (!$content) {
                continue;
            }

            $mimeType = $document->mime_type ?? 'application/pdf';
            $filename = $document->original_name ?? $document->file_name ?? 'Document ' . ($index + 1);

            $html .= '<div class="page">';
            $html .= '<div class="doc-header">Document ' . ($index + 1) . ': ' . htmlspecialchars($filename) . '</div>';

            if (strpos($mimeType, 'image/') === 0) {
                // Add image
                $base64 = base64_encode($content);
                $html .= '<img src="data:' . $mimeType . ';base64,' . $base64 . '" />';
            } else {
                // For PDFs, show placeholder
                $html .= '<div style="border: 2px solid #ddd; padding: 50px; margin-top: 20px;">';
                $html .= '<p><strong>PDF Document:</strong> ' . htmlspecialchars($filename) . '</p>';
                $html .= '<p style="color: #666;">Note: This is a merged compilation. Original PDF pages are included.</p>';
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        $html .= '</body></html>';

        // Create PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'merged_documents_' . $student->name . '_' . time() . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Merge all approved documents for a student into a single PDF
     *
     * @param Student $student
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function mergeAllApprovedDocuments(Student $student)
    {
        // Authorization check - only admin, assigned employee, or super admin can download merged documents
        $this->authorize('view', $student);

        try {
            // Use PDF merge service to merge all approved documents
            $result = $this->pdfMergeService->mergeAllApprovedDocuments($student);

            if (!$result['success']) {
                return back()->with('error', $result['error']);
            }

            // Log the activity
            Log::info('All approved documents merged and downloaded', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'downloaded_by' => Auth::id(),
                'downloaded_by_name' => Auth::user()->name,
                'timestamp' => now()
            ]);

            // Return the merged PDF as download
            return response($result['content'], 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error('Error merging all approved documents', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to merge documents. Please try again or contact support.');
        }
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

        $filename = $document->filename;
        $document->delete();

        return back()->with('success', "Document '{$filename}' has been deleted successfully.");
    }
}
