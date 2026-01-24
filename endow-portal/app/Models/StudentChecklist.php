<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentChecklist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'checklist_item_id',
        'status',
        'document_path',
        'document_data',
        'document_mime_type',
        'document_original_name',
        'remarks',
        'feedback',
        'approved_by',
        'approved_at',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'deadline',
        'is_overdue',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_overdue' => 'boolean',
    ];

    /**
     * Get the student this checklist belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the checklist item.
     */
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Get the user who approved this checklist.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who reviewed this checklist.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get all documents for this checklist.
     */
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * Check if checklist is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if checklist is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if checklist is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if checklist is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the applicable deadline for this checklist item based on the student's program.
     * Returns the deadline as a Carbon date instance or null if no deadline is set.
     */
    public function getApplicableDeadline()
    {
        if (!$this->student || !$this->student->targetProgram) {
            return null;
        }

        // Check if there's a program-specific deadline for this document
        $programDeadline = $this->student->targetProgram->documentDeadlines()
            ->where('checklist_item_id', $this->checklist_item_id)
            ->first();

        if ($programDeadline && $programDeadline->has_specific_deadline && $programDeadline->specific_deadline) {
            return $programDeadline->specific_deadline;
        }

        // Fall back to program's default deadline
        if ($this->student->targetProgram->default_deadline) {
            return $this->student->targetProgram->default_deadline;
        }

        return null;
    }

    /**
     * Check if this checklist item's deadline is approaching (within 7 days).
     */
    public function isDeadlineApproaching(): bool
    {
        $deadline = $this->getApplicableDeadline();
        if (!$deadline) {
            return false;
        }

        $daysUntilDeadline = now()->diffInDays($deadline, false);
        return $daysUntilDeadline >= 0 && $daysUntilDeadline <= 7;
    }

    /**
     * Check if this checklist item's deadline has passed.
     */
    public function isDeadlinePassed(): bool
    {
        $deadline = $this->getApplicableDeadline();
        if (!$deadline) {
            return false;
        }

        return now()->greaterThan($deadline);
    }

    /**
     * Get formatted deadline string with status indicator.
     */
    public function getFormattedDeadlineAttribute(): ?string
    {
        $deadline = $this->getApplicableDeadline();
        if (!$deadline) {
            return null;
        }

        $formatted = $deadline->format('M d, Y');

        if ($this->isDeadlinePassed()) {
            return "$formatted (Overdue)";
        } elseif ($this->isDeadlineApproaching()) {
            return "$formatted (Approaching)";
        }

        return $formatted;
    }
}
