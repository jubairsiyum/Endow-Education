<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Daily Report Approver Model
 * 
 * Multi-level approval workflow
 * Supports hierarchical approval chains
 */
class DailyReportApprover extends Model
{
    use HasFactory;

    protected $table = 'office_daily_report_approvers';

    protected $fillable = [
        'daily_report_id',
        'approver_id',
        'approval_level',
        'status',
        'comments',
        'responded_at',
        'notified_at',
        'reminder_count',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'notified_at' => 'datetime',
        'reminder_count' => 'integer',
    ];

    /**
     * Get the daily report
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    /**
     * Get the approver user
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Scope: Pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: By approver
     */
    public function scopeByApprover($query, $approverId)
    {
        return $query->where('approver_id', $approverId);
    }

    /**
     * Scope: Current level (for sequential approval)
     */
    public function scopeCurrentLevel($query, $level)
    {
        return $query->where('approval_level', $level);
    }

    /**
     * Check if approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Mark as notified
     */
    public function markAsNotified(): void
    {
        $this->update([
            'notified_at' => now(),
        ]);
    }

    /**
     * Increment reminder count
     */
    public function incrementReminder(): void
    {
        $this->increment('reminder_count');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'skipped' => 'secondary',
            default => 'secondary',
        };
    }
}
