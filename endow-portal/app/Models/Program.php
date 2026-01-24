<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'university_id',
        'name',
        'code',
        'level',
        'duration',
        'description',
        'tuition_fee',
        'currency',
        'is_active',
        'order',
        'created_by',
        'default_deadline',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
        'default_deadline' => 'date',
    ];

    /**
     * Get the university that owns the program.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the checklist items for this program.
     */
    public function checklistItems()
    {
        return $this->belongsToMany(ChecklistItem::class, 'checklist_program');
    }

    /**
     * Get document deadlines for this program.
     */
    public function documentDeadlines()
    {
        return $this->hasMany(ProgramDocumentDeadline::class);
    }

    /**
     * Get deadline for a specific checklist item in this program.
     * Returns specific deadline if set, otherwise returns program default.
     */
    public function getDeadlineForDocument($checklistItemId)
    {
        $documentDeadline = $this->documentDeadlines()
            ->where('checklist_item_id', $checklistItemId)
            ->first();

        if ($documentDeadline && $documentDeadline->has_specific_deadline) {
            return $documentDeadline->specific_deadline;
        }

        return $this->default_deadline;
    }

    /**
     * Get formatted default deadline
     */
    public function getFormattedDefaultDeadlineAttribute()
    {
        if (!$this->default_deadline) {
            return 'No deadline set';
        }
        return $this->default_deadline->format('M d, Y');
    }

    /**
     * Check if program has any document-specific deadlines
     */
    public function hasDocumentSpecificDeadlines()
    {
        return $this->documentDeadlines()
            ->where('has_specific_deadline', true)
            ->exists();
    }

    /**
     * Get the students targeting this program.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'target_program_id');
    }

    /**
     * Get the user who created this program.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by custom order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get the formatted tuition fee.
     */
    public function getFormattedTuitionFeeAttribute()
    {
        if (!$this->tuition_fee) {
            return 'N/A';
        }
        return $this->currency . ' ' . number_format($this->tuition_fee, 2);
    }
}
