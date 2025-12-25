<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ChecklistItem;
use App\Models\StudentChecklist;
use Illuminate\Support\Facades\DB;

class ChecklistService
{
    /**
     * Initialize checklists for a student
     * Creates StudentChecklist entries for all active checklist items
     *
     * @param Student $student
     * @return void
     */
    public function initializeChecklistsForStudent(Student $student): void
    {
        $checklistItems = ChecklistItem::active()->ordered()->get();

        foreach ($checklistItems as $item) {
            StudentChecklist::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'checklist_item_id' => $item->id,
                ],
                [
                    'status' => 'pending',
                ]
            );
        }
    }

    /**
     * Get checklist progress for a student
     *
     * @param Student $student
     * @return array
     */
    public function getChecklistProgress(Student $student): array
    {
        $total = $student->checklists()->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'pending' => 0,
                'submitted' => 0,
                'approved' => 0,
                'rejected' => 0,
                'percentage' => 0,
            ];
        }

        $pending = $student->checklists()->where('status', 'pending')->count();
        $submitted = $student->checklists()->where('status', 'submitted')->count();
        $approved = $student->checklists()->where('status', 'approved')->count();
        $rejected = $student->checklists()->where('status', 'rejected')->count();

        $percentage = (int) (($approved / $total) * 100);

        return [
            'total' => $total,
            'pending' => $pending,
            'submitted' => $submitted,
            'approved' => $approved,
            'rejected' => $rejected,
            'percentage' => $percentage,
        ];
    }

    /**
     * Update checklist status
     *
     * @param StudentChecklist $checklist
     * @param string $status
     * @param string|null $remarks
     * @param int|null $approvedBy
     * @return StudentChecklist
     */
    public function updateChecklistStatus(
        StudentChecklist $checklist,
        string $status,
        ?string $remarks = null,
        ?int $approvedBy = null
    ): StudentChecklist {
        $checklist->status = $status;
        
        if ($remarks) {
            $checklist->remarks = $remarks;
        }

        if (in_array($status, ['approved', 'rejected']) && $approvedBy) {
            $checklist->approved_by = $approvedBy;
            $checklist->approved_at = now();
        }

        $checklist->save();

        return $checklist;
    }

    /**
     * Check if student has submitted all required checklists
     *
     * @param Student $student
     * @return bool
     */
    public function hasSubmittedAllRequired(Student $student): bool
    {
        $requiredItems = ChecklistItem::active()
            ->where('is_required', true)
            ->pluck('id');

        if ($requiredItems->isEmpty()) {
            return true;
        }

        $submittedCount = $student->checklists()
            ->whereIn('checklist_item_id', $requiredItems)
            ->whereIn('status', ['submitted', 'approved'])
            ->count();

        return $submittedCount === $requiredItems->count();
    }

    /**
     * Get pending checklists for a student
     *
     * @param Student $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingChecklists(Student $student)
    {
        return $student->checklists()
            ->with('checklistItem')
            ->where('status', 'pending')
            ->get();
    }

    /**
     * Get completed checklists count by status
     *
     * @param Student $student
     * @return array
     */
    public function getStatusCounts(Student $student): array
    {
        return DB::table('student_checklists')
            ->select('status', DB::raw('count(*) as count'))
            ->where('student_id', $student->id)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
