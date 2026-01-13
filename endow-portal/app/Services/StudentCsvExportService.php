<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentCsvExportService
{
    /**
     * Available columns for export
     */
    protected array $availableColumns = [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'country' => 'Country',
        'passport_number' => 'Passport Number',
        'university' => 'University',
        'program' => 'Program',
        'ssc_year' => 'SSC Year',
        'ssc_result' => 'SSC Result',
        'hsc_year' => 'HSC Year',
        'hsc_result' => 'HSC Result',
        'has_ielts' => 'Has IELTS',
        'ielts_score' => 'IELTS Score',
        'status' => 'Status',
        'account_status' => 'Account Status',
        'assigned_to' => 'Assigned To',
        'created_by' => 'Created By',
        'created_at' => 'Registration Date',
        'updated_at' => 'Last Updated',
    ];

    /**
     * Get available columns for selection
     */
    public function getAvailableColumns(): array
    {
        return $this->availableColumns;
    }

    /**
     * Export students to CSV
     *
     * @param array $studentIds Array of student IDs to export (empty = all)
     * @param array $columns Columns to include in export (empty = all)
     * @return StreamedResponse
     */
    public function export(array $studentIds = [], array $columns = []): StreamedResponse
    {
        // If no columns specified, use all available columns
        if (empty($columns)) {
            $columns = array_keys($this->availableColumns);
        }

        // Validate columns
        $columns = array_intersect($columns, array_keys($this->availableColumns));

        $fileName = 'students_export_' . date('Y-m-d_His') . '.csv';

        return Response::stream(function () use ($studentIds, $columns) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header row
            $headers = [];
            foreach ($columns as $column) {
                $headers[] = $this->availableColumns[$column] ?? $column;
            }
            fputcsv($handle, $headers);

            // Build query
            $query = Student::with(['university', 'program', 'assignedUser', 'creator']);

            // Filter by IDs if provided
            if (!empty($studentIds)) {
                $query->whereIn('id', $studentIds);
            }

            // Stream data in chunks to avoid memory issues
            $query->chunk(500, function ($students) use ($handle, $columns) {
                foreach ($students as $student) {
                    $row = [];
                    foreach ($columns as $column) {
                        $row[] = $this->getColumnValue($student, $column);
                    }
                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Get formatted value for a specific column
     */
    protected function getColumnValue(Student $student, string $column): string
    {
        return match ($column) {
            'id' => (string) $student->id,
            'name' => $student->name ?? '',
            'email' => $student->email ?? '',
            'phone' => $student->phone ?? '',
            'country' => $student->country ?? '',
            'passport_number' => $student->passport_number ?? '',
            'university' => $student->university?->name ?? '',
            'program' => $student->program?->name ?? '',
            'ssc_year' => $student->ssc_year ?? '',
            'ssc_result' => $student->ssc_result ?? '',
            'hsc_year' => $student->hsc_year ?? '',
            'hsc_result' => $student->hsc_result ?? '',
            'has_ielts' => $student->has_ielts ? 'Yes' : 'No',
            'ielts_score' => $student->ielts_score ?? '',
            'status' => $student->status ?? '',
            'account_status' => $student->account_status ?? '',
            'assigned_to' => $student->assignedUser?->name ?? '',
            'created_by' => $student->creator?->name ?? '',
            'created_at' => $student->created_at?->format('Y-m-d H:i:s') ?? '',
            'updated_at' => $student->updated_at?->format('Y-m-d H:i:s') ?? '',
            default => '',
        };
    }

    /**
     * Get count of students to be exported
     */
    public function getExportCount(array $studentIds = []): int
    {
        $query = Student::query();

        if (!empty($studentIds)) {
            $query->whereIn('id', $studentIds);
        }

        return $query->count();
    }
}
