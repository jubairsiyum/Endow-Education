<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItemDeadline extends Model
{
    use HasFactory;

    protected $table = 'checklist_item_deadlines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'checklist_item_id',
        'program_id',
        'deadline_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the checklist item this deadline belongs to.
     */
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Get the program this deadline is for.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get formatted deadline date
     */
    public function getFormattedDeadlineAttribute()
    {
        if (!$this->deadline_date) {
            return 'No deadline';
        }
        return $this->deadline_date->format('M d, Y');
    }

    /**
     * Check if this deadline has passed
     */
    public function isOverdue()
    {
        return now()->isAfter($this->deadline_date);
    }

    /**
     * Check if deadline is approaching (within 7 days)
     */
    public function isApproaching()
    {
        $daysUntilDeadline = now()->diffInDays($this->deadline_date);
        return $daysUntilDeadline <= 7 && $daysUntilDeadline >= 0;
    }
}
