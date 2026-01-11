<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Import FPDF-based FPDI
require_once __DIR__ . '/../../vendor/setasign/fpdi/src/autoload.php';

class PdfMergeService
{
    /**
     * Merge all approved documents for a student into a single PDF
     *
     * @param Student $student
     * @return array ['success' => bool, 'content' => string|null, 'filename' => string|null, 'error' => string|null]
     */
    public function mergeAllApprovedDocuments(Student $student): array
    {
        try {
            // Get all approved documents for the student
            $documents = StudentDocument::where('student_id', $student->id)
                ->where('status', 'approved')
                ->with('checklistItem')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($documents->isEmpty()) {
                return [
                    'success' => false,
                    'content' => null,
                    'filename' => null,
                    'error' => 'No approved documents found to merge.'
                ];
            }

            // Check if all documents are PDFs
            foreach ($documents as $document) {
                if (!$this->isPdf($document)) {
                    return [
                        'success' => false,
                        'content' => null,
                        'filename' => null,
                        'error' => 'All documents must be in PDF format to merge.'
                    ];
                }
            }

            // Merge PDFs
            $mergedPdf = $this->mergePdfs($documents);

            if (!$mergedPdf) {
                return [
                    'success' => false,
                    'content' => null,
                    'filename' => null,
                    'error' => 'Failed to merge PDF documents.'
                ];
            }

            $filename = $this->generateMergedFilename($student);

            return [
                'success' => true,
                'content' => $mergedPdf,
                'filename' => $filename,
                'error' => null
            ];
        } catch (\Exception $e) {
            Log::error('PDF Merge Error: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'content' => null,
                'filename' => null,
                'error' => 'An error occurred while merging documents: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if document is a PDF
     *
     * @param StudentDocument $document
     * @return bool
     */
    private function isPdf(StudentDocument $document): bool
    {
        return $document->mime_type === 'application/pdf' ||
               strtolower(pathinfo($document->filename, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Merge multiple PDF documents into one
     *
     * @param \Illuminate\Support\Collection $documents
     * @return string|false PDF content as binary string or false on failure
     */
    private function mergePdfs($documents)
    {
        try {
            $pdf = new \setasign\Fpdi\Fpdi();

            // Disable auto page breaks and margins to prevent gaps
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->SetMargins(0, 0, 0);

            foreach ($documents as $index => $document) {
                // Get document content
                if ($document->file_data) {
                    $fileContent = base64_decode($document->file_data);
                } elseif ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    $fileContent = Storage::disk('public')->get($document->file_path);
                } else {
                    Log::warning('Document file not found during merge', [
                        'document_id' => $document->id,
                        'student_id' => $document->student_id
                    ]);
                    continue;
                }

                // Create temporary file for FPDI
                $tempFile = tempnam(sys_get_temp_dir(), 'pdf_merge_');
                file_put_contents($tempFile, $fileContent);

                try {
                    // Get page count
                    $pageCount = $pdf->setSourceFile($tempFile);

                    // Import all pages from this document
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($templateId);

                        // Determine orientation based on page dimensions
                        $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                        // Add a new page with the exact dimensions of the source page
                        $pdf->AddPage($orientation, [$size['width'], $size['height']]);

                        // Use the imported page template at position 0,0 with full original dimensions
                        // Pass the template size to ensure proper rendering
                        $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

                        // Add footer with document info on first page of each document
                        if ($pageNo === 1) {
                            $this->addDocumentHeader($pdf, $document, $index + 1, $documents->count());
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing document during merge: ' . $e->getMessage(), [
                        'document_id' => $document->id
                    ]);
                } finally {
                    // Clean up temp file
                    if (file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                }
            }

            // Return PDF as string
            return $pdf->Output('S');
        } catch (\Exception $e) {
            Log::error('PDF Merge Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Add header with document information
     *
     * @param \setasign\Fpdi\Fpdi $pdf
     * @param StudentDocument $document
     * @param int $docNumber
     * @param int $totalDocs
     * @return void
     */
    private function addDocumentHeader($pdf, StudentDocument $document, int $docNumber, int $totalDocs): void
    {
        try {
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetTextColor(100, 100, 100);

            // Add document info at the bottom
            $y = $pdf->GetPageHeight() - 10;
            $pdf->SetXY(10, $y);

            $checklistTitle = $document->checklistItem ? $document->checklistItem->title : 'Document';
            $text = "Document {$docNumber} of {$totalDocs}: {$checklistTitle}";
            $pdf->Cell(0, 5, $text, 0, 0, 'L');

            // Reset text color
            $pdf->SetTextColor(0, 0, 0);
        } catch (\Exception $e) {
            // Fail silently - header is not critical
            Log::debug('Could not add document header: ' . $e->getMessage());
        }
    }

    /**
     * Generate filename for merged PDF
     *
     * @param Student $student
     * @return string
     */
    private function generateMergedFilename(Student $student): string
    {
        $studentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->name);
        $date = now()->format('Y-m-d');
        return "{$studentName}_All_Documents_{$date}.pdf";
    }

    /**
     * Check if all student documents are approved
     *
     * @param Student $student
     * @return bool
     */
    public function allDocumentsApproved(Student $student): bool
    {
        // Get total required documents (those linked to checklists)
        $totalDocuments = StudentDocument::where('student_id', $student->id)
            ->whereNotNull('student_checklist_id')
            ->count();

        if ($totalDocuments === 0) {
            return false;
        }

        // Get approved documents count
        $approvedDocuments = StudentDocument::where('student_id', $student->id)
            ->whereNotNull('student_checklist_id')
            ->where('status', 'approved')
            ->count();

        return $totalDocuments === $approvedDocuments;
    }

    /**
     * Get document statistics for a student
     *
     * @param Student $student
     * @return array
     */
    public function getDocumentStatistics(Student $student): array
    {
        $total = StudentDocument::where('student_id', $student->id)
            ->whereNotNull('student_checklist_id')
            ->count();

        $approved = StudentDocument::where('student_id', $student->id)
            ->whereNotNull('student_checklist_id')
            ->where('status', 'approved')
            ->count();

        $pending = StudentDocument::where('student_id', $student->id)
            ->whereNotNull('student_checklist_id')
            ->where('status', 'submitted')
            ->count();

        $rejected = StudentDocument::where('student_id', $student->id)
            ->whereNotNull('student_checklist_id')
            ->where('status', 'rejected')
            ->count();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'all_approved' => $total > 0 && $total === $approved,
        ];
    }
}
