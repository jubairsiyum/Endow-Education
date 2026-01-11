<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultantEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'consultant_id',
        'question_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'consultant_id' => 'integer',
        'question_id' => 'integer',
    ];

    /**
     * Rating labels for display.
     */
    public const RATINGS = [
        'below_average' => 'Below Average',
        'average' => 'Average',
        'neutral' => 'Neutral',
        'good' => 'Good',
        'excellent' => 'Excellent',
    ];

    /**
     * Rating colors for badges.
     */
    public const RATING_COLORS = [
        'below_average' => 'danger',
        'average' => 'warning',
        'neutral' => 'secondary',
        'good' => 'info',
        'excellent' => 'success',
    ];

    /**
     * Get the student who submitted the evaluation.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the consultant being evaluated.
     */
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Get the question being answered.
     */
    public function question()
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }

    /**
     * Get formatted rating label.
     */
    public function getRatingLabelAttribute()
    {
        return self::RATINGS[$this->rating] ?? $this->rating;
    }

    /**
     * Get rating color for badge.
     */
    public function getRatingColorAttribute()
    {
        return self::RATING_COLORS[$this->rating] ?? 'secondary';
    }

    /**
     * Scope to filter by consultant.
     */
    public function scopeForConsultant($query, $consultantId)
    {
        return $query->where('consultant_id', $consultantId);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
