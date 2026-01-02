<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'student_id_number',
        'academic_level',
        'major',
        'minor',
        'gpa',
        'enrollment_date',
        'expected_graduation_date',
        'bio',
        'interests',
        'skills',
        'languages',
        'social_links',
        'preferences',
        'profile_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enrollment_date' => 'date',
        'expected_graduation_date' => 'date',
        'gpa' => 'decimal:2',
        'languages' => 'array',
        'social_links' => 'array',
        'preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the profile.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get formatted GPA.
     */
    public function getFormattedGpaAttribute(): string
    {
        return $this->gpa ? number_format((float) $this->gpa, 2) : 'N/A';
    }

    /**
     * Check if profile is complete.
     */
    public function isComplete(): bool
    {
        $requiredFields = [
            'academic_level',
            'major',
            'enrollment_date',
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get profile completion percentage.
     */
    public function getCompletionPercentage(): int
    {
        $fields = [
            'student_id_number',
            'academic_level',
            'major',
            'gpa',
            'enrollment_date',
            'expected_graduation_date',
            'bio',
            'interests',
            'skills',
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return (int) (($completed / count($fields)) * 100);
    }
}
