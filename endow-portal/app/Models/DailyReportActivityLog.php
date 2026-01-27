<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Daily Report Activity Log Model
 * 
 * Comprehensive audit trail for all report activities
 * Tracks changes for compliance and accountability
 */
class DailyReportActivityLog extends Model
{
    use HasFactory;

    protected $table = 'office_daily_report_activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'daily_report_id',
        'user_id',
        'action',
        'description',
        'changes',
        'metadata',
        'ip_address',
        'performed_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'metadata' => 'array',
        'performed_at' => 'datetime',
    ];

    /**
     * Get the daily report this log belongs to
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'fas fa-plus-circle text-success',
            'updated' => 'fas fa-edit text-primary',
            'submitted' => 'fas fa-paper-plane text-info',
            'reviewed' => 'fas fa-eye text-warning',
            'approved' => 'fas fa-check-circle text-success',
            'rejected' => 'fas fa-times-circle text-danger',
            'deleted' => 'fas fa-trash text-danger',
            'restored' => 'fas fa-undo text-success',
            default => 'fas fa-circle text-secondary',
        };
    }

    /**
     * Get formatted action name
     */
    public function getActionNameAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    /**
     * Create activity log entry
     */
    public static function log(
        int $reportId, 
        int $userId, 
        string $action, 
        ?string $description = null,
        ?array $changes = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'daily_report_id' => $reportId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'changes' => $changes,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'performed_at' => now(),
        ]);
    }
}
