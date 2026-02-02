<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Daily Report Review Model
 *
 * Tracks all review history for daily reports, allowing multiple
 * rounds of feedback from managers.
 */
class DailyReportReview extends Model
{
    use HasFactory;

    protected $table = 'office_daily_report_reviews';

    protected $fillable = [
        'daily_report_id',
        'reviewer_id',
        'comment',
        'marked_as_completed',
        'reviewed_at',
    ];

    protected $casts = [
        'marked_as_completed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the daily report this review belongs to
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    /**
     * Get the user who reviewed
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Check if this review marked the report as completed
     */
    public function isCompletionReview(): bool
    {
        return $this->marked_as_completed === true;
    }
}
