<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Daily Report Template Model
 * 
 * Reusable templates for recurring reports
 * Helps standardize reporting across departments
 */
class DailyReportTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'office_daily_report_templates';

    protected $fillable = [
        'department_id',
        'name',
        'description',
        'content',
        'fields',
        'default_tags',
        'frequency',
        'created_by',
        'is_active',
        'is_mandatory',
        'usage_count',
    ];

    protected $casts = [
        'fields' => 'array',
        'default_tags' => 'array',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * Get the department this template belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Mandatory templates
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope: By department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get frequency badge color
     */
    public function getFrequencyBadgeAttribute(): string
    {
        return match($this->frequency) {
            'daily' => 'info',
            'weekly' => 'primary',
            'monthly' => 'success',
            'custom' => 'secondary',
            default => 'secondary',
        };
    }
}
