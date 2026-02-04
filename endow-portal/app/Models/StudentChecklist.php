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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->hasMany(StudentDocument::class, 'student_checklist_id');
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
}
