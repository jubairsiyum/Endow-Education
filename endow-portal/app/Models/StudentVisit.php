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
    public const STATUS_PROSPECTIVE = 'Prospective';
    public const STATUS_UNDER_REVIEW = 'Under Review';
    public const STATUS_ELIGIBILITY_CONFIRMED = 'Eligibility Confirmed';
    public const STATUS_NEEDS_COUNSELING = 'Needs Counseling';
    public const STATUS_NOT_ELIGIBLE = 'Not Eligible';

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
        'prospective_status',
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
    public static function getStatuses()
    {
        return [
            self::STATUS_PROSPECTIVE,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_ELIGIBILITY_CONFIRMED,
            self::STATUS_NEEDS_COUNSELING,
            self::STATUS_NOT_ELIGIBLE,
        ];
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->prospective_status) {
            self::STATUS_PROSPECTIVE => 'primary',
            self::STATUS_UNDER_REVIEW => 'warning',
            self::STATUS_ELIGIBILITY_CONFIRMED => 'success',
            self::STATUS_NEEDS_COUNSELING => 'info',
            self::STATUS_NOT_ELIGIBLE => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status description.
     */
    public function getStatusDescriptionAttribute()
    {
        return match($this->prospective_status) {
            self::STATUS_PROSPECTIVE => 'Potential Student – Appears eligible and interested',
            self::STATUS_UNDER_REVIEW => 'Profile evaluation in progress',
            self::STATUS_ELIGIBILITY_CONFIRMED => 'Meets basic requirements',
            self::STATUS_NEEDS_COUNSELING => 'Requires detailed guidance',
            self::STATUS_NOT_ELIGIBLE => 'Does not meet criteria',
            default => '',
        };
    }
}
