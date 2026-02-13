<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Office Daily Report Model
 *
 * Represents daily reports submitted by different departments
 * for the Office Management System.
 */
class DailyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'office_daily_reports';

    protected $fillable = [
        'department_id',
        'parent_report_id',
        'title',
        'description',
        'tags',
        'report_date',
        'estimated_completion_date',
        'submitted_by',
        'reviewed_by',
        'approved_by',
        'status',
        'priority',
        'review_comment',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'is_template',
    ];

    protected $casts = [
        'report_date' => 'date',
        'estimated_completion_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'tags' => 'array',
        'is_template' => 'boolean',
    ];

    /**
     * Status options - Professional workflow
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_REVIEW = 'review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Priority levels
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    /**
     * Get the department this report belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who submitted the report
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who reviewed the report
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who approved the report
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get parent report (for follow-ups)
     */
    public function parentReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'parent_report_id');
    }

    /**
     * Get follow-up reports
     */
    public function followUpReports()
    {
        return $this->hasMany(DailyReport::class, 'parent_report_id');
    }

    /**
     * Get all reviews for this report
     */
    public function reviews()
    {
        return $this->hasMany(DailyReportReview::class, 'daily_report_id')->orderBy('reviewed_at', 'desc');
    }

    /**
     * Get all attachments
     */
    public function attachments()
    {
        return $this->hasMany(DailyReportAttachment::class, 'daily_report_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get all comments (collaborative feedback)
     */
    public function comments()
    {
        return $this->hasMany(DailyReportComment::class, 'daily_report_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get work assignments linked to this report
     */
    public function workAssignments()
    {
        return $this->hasMany(WorkAssignment::class, 'daily_report_id');
    }

    /**
     * Get activity logs (audit trail)
     */
    public function activityLogs()
    {
        return $this->hasMany(DailyReportActivityLog::class, 'daily_report_id')->orderBy('performed_at', 'desc');
    }

    /**
     * Get approvers in the approval chain
     */
    public function approvers()
    {
        return $this->hasMany(DailyReportApprover::class, 'daily_report_id')->orderBy('approval_level', 'asc');
    }

    /**
     * Scope: Filter by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->when($departmentId, function ($q) use ($departmentId) {
            return $q->where('department_id', $departmentId);
        });
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            return $q->whereBetween('report_date', [$startDate, $endDate]);
        });
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->when($status, function ($q) use ($status) {
            return $q->where('status', $status);
        });
    }

    /**
     * Scope: Get in progress reports
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope: Get reports in review
     */
    public function scopeInReview($query)
    {
        return $query->where('status', self::STATUS_REVIEW);
    }

    /**
     * Scope: Get completed reports
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Filter by submitted user
     */
    public function scopeBySubmitter($query, $userId)
    {
        return $query->when($userId, function ($q) use ($userId) {
            return $q->where('submitted_by', $userId);
        });
    }

    /**
     * Check if report is a draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if report is submitted
     */
    public function isSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_PENDING_REVIEW]);
    }

    /**
     * Check if report is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if report is in review
     */
    public function isInReview(): bool
    {
        return in_array($this->status, [self::STATUS_REVIEW, self::STATUS_PENDING_REVIEW]);
    }

    /**
     * Check if report is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if report is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if report is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if report is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if report is pending (for backward compatibility)
     */
    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_PENDING_REVIEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
        ]);
    }

    /**
     * Check if report is awaiting review by managers/supervisors
     * Excludes drafts which haven't been submitted yet
     */
    public function isAwaitingReview(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_PENDING_REVIEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
        ]);
    }

    /**
     * Check if report is reviewed (for backward compatibility)
     */
    public function isReviewed(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Check if report can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_REJECTED,
        ]);
    }

    /**
     * Check if report can be deleted
     */
    public function canBeDeleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_REJECTED,
        ]);
    }

    /**
     * Priority checking methods
     */
    public function isUrgent(): bool
    {
        return $this->priority === self::PRIORITY_URGENT;
    }

    public function isHighPriority(): bool
    {
        return $this->priority === self::PRIORITY_HIGH;
    }

    /**
     * Get formatted department name
     */
    public function getDepartmentNameAttribute(): string
    {
        return $this->department?->name ?? 'N/A';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_PENDING_REVIEW => 'warning',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_REVIEW => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get formatted status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_PENDING_REVIEW => 'Pending Review',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'danger',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_NORMAL => 'info',
            self::PRIORITY_LOW => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get formatted priority text
     */
    public function getPriorityTextAttribute(): string
    {
        return ucfirst($this->priority ?? 'normal');
    }
}
