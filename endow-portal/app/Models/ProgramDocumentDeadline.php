<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramDocumentDeadline extends Model
{
    use HasFactory;

    protected $table = 'program_document_deadlines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'checklist_item_id',
        'has_specific_deadline',
        'specific_deadline',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_specific_deadline' => 'boolean',
        'specific_deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the program this deadline belongs to.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the checklist item (document) this deadline is for.
     */
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Get the applicable deadline (specific if set, otherwise program default)
     */
    public function getApplicableDeadline()
    {
        if ($this->has_specific_deadline && $this->specific_deadline) {
            return $this->specific_deadline;
        }

        return $this->program->default_deadline;
    }

    /**
     * Get formatted deadline for display
     */
    public function getFormattedDeadlineAttribute()
    {
        $deadline = $this->getApplicableDeadline();
        
        if (!$deadline) {
            return 'No deadline';
        }

        return $deadline->format('M d, Y');
    }

    /**
     * Check if deadline is approaching (within 7 days)
     */
    public function isApproaching()
    {
        $deadline = $this->getApplicableDeadline();
        
        if (!$deadline) {
            return false;
        }

        $daysUntilDeadline = now()->diffInDays($deadline);
        return $daysUntilDeadline <= 7 && $daysUntilDeadline >= 0;
    }

    /**
     * Check if deadline has passed
     */
    public function isOverdue()
    {
        $deadline = $this->getApplicableDeadline();
        
        if (!$deadline) {
            return false;
        }

        return now()->isAfter($deadline);
    }
}
