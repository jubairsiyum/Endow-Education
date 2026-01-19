<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentVisit extends Model
{
    use HasFactory;

    /**
     * Prospective status constants
     */
    public const STATUS_PROSPECTIVE_HOT = 'prospective_hot';
    public const STATUS_PROSPECTIVE_WARM = 'prospective_warm';
    public const STATUS_PROSPECTIVE_COLD = 'prospective_cold';
    public const STATUS_PROSPECTIVE_NOT_INTERESTED = 'prospective_not_interested';
    public const STATUS_CONFIRMED_STUDENT = 'confirmed_student';

    /**
     * Status metadata for labels, colors and descriptions.
     * Color values are Bootstrap contextual color names.
     */
    private const STATUS_META = [
        self::STATUS_PROSPECTIVE_HOT => [
            'label' => 'Prospective: Hot (Ready to enroll)',
            'color' => 'success',
            'description' => 'High intent; ready to enroll with minimal follow-up.',
        ],
        self::STATUS_PROSPECTIVE_WARM => [
            'label' => 'Prospective: Warm (Interested)',
            'color' => 'warning',
            'description' => 'Interested and engaged; needs follow-up.',
        ],
        self::STATUS_PROSPECTIVE_COLD => [
            'label' => 'Prospective: Cold (Low interest)',
            'color' => 'secondary',
            'description' => 'Low intent; continue nurturing and checking in periodically.',
        ],
        self::STATUS_PROSPECTIVE_NOT_INTERESTED => [
            'label' => 'Prospective: Not Interested',
            'color' => 'danger',
            'description' => 'Not interested at this time.',
        ],
        self::STATUS_CONFIRMED_STUDENT => [
            'label' => 'Confirmed Student',
            'color' => 'primary',
            'description' => 'Enrollment confirmed.',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_name',
        'phone',
        'email',
        'prospective_status',
        'employee_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee assigned to this visit.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Scope a query to only include visits by a specific employee.
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope a query to search visits.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('student_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Get all available prospective statuses.
     */
    public static function getStatuses(): array
    {
        return array_keys(self::STATUS_META);
    }

    /**
     * Get a human-friendly label for a status value.
     */
    public static function getStatusLabel(string $status): string
    {
        return self::STATUS_META[$status]['label'] ?? ucwords(str_replace('_', ' ', $status));
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return self::STATUS_META[$this->prospective_status]['color'] ?? 'secondary';
    }

    /**
     * Get status description.
     */
    public function getStatusDescriptionAttribute()
    {
        return self::STATUS_META[$this->prospective_status]['description'] ?? '';
    }

    /**
     * Human-friendly status label accessor.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusLabel($this->prospective_status ?? '') ?? 'Not set';
    }
}
