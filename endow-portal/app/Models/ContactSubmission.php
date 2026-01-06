<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'subject',
        'priority',
        'message',
        'status',
        'assigned_to',
        'admin_notes',
        'responded_at',
        'responded_by',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student who submitted the contact form.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user assigned to handle this submission.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who responded to this submission.
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Get formatted subject label.
     */
    public function getSubjectLabelAttribute()
    {
        $labels = [
            'document_issue' => 'Document Issue',
            'application_status' => 'Application Status',
            'technical_problem' => 'Technical Problem',
            'program_change' => 'Program Change Request',
            'general_inquiry' => 'General Inquiry',
            'other' => 'Other',
        ];

        return $labels[$this->subject] ?? ucfirst(str_replace('_', ' ', $this->subject));
    }

    /**
     * Get badge color for priority.
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            default => 'info',
        };
    }

    /**
     * Get badge color for status.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'primary',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'info',
        };
    }
}
