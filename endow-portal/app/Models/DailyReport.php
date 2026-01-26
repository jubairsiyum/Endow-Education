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
        'title',
        'description',
        'report_date',
        'submitted_by',
        'reviewed_by',
        'status',
        'review_comment',
        'reviewed_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Status options
     */
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_REVIEW = 'review';
    public const STATUS_COMPLETED = 'completed';

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
        return $this->status === self::STATUS_REVIEW;
    }

    /**
     * Check if report is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if report is pending (for backward compatibility)
     */
    public function isPending(): bool
    {
        return $this->isInProgress() || $this->isInReview();
    }

    /**
     * Check if report is reviewed (for backward compatibility)
     */
    public function isReviewed(): bool
    {
        return $this->isCompleted();
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
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_REVIEW => 'warning',
            self::STATUS_COMPLETED => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get formatted status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_REVIEW => 'Under Review',
            self::STATUS_COMPLETED => 'Completed',
            default => ucfirst($this->status),
        };
    }
}
