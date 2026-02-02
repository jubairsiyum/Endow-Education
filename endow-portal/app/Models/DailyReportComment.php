<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Daily Report Comment Model
 * 
 * Collaborative commenting system for reports
 * Supports threaded discussions
 */
class DailyReportComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'office_daily_report_comments';

    protected $fillable = [
        'daily_report_id',
        'user_id',
        'comment',
        'parent_comment_id',
        'type',
        'is_internal',
        'is_read',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_read' => 'boolean',
    ];

    /**
     * Get the daily report this comment belongs to
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    /**
     * Get the user who made the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for threaded comments)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DailyReportComment::class, 'parent_comment_id');
    }

    /**
     * Get replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(DailyReportComment::class, 'parent_comment_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Scope: Get only top-level comments (no parent)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_comment_id');
    }

    /**
     * Scope: Get visible comments (non-internal or user is authorized)
     */
    public function scopeVisible($query, $userId = null)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('is_internal', false);
            
            // If user is provided, show their internal comments
            if ($userId) {
                $q->orWhere(function($subQ) use ($userId) {
                    $subQ->where('is_internal', true)
                        ->where('user_id', $userId);
                });
            }
        });
    }

    /**
     * Get comment type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'approval' => 'success',
            'rejection' => 'danger',
            'question' => 'warning',
            'note' => 'info',
            'feedback' => 'primary',
            default => 'secondary',
        };
    }
}
