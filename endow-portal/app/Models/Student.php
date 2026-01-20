<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'given_names',
        'father_name',
        'mother_name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'passport_number',
        'passport_expiry_date',
        'nationality',
        'country',
        'address',
        'city',
        'postal_code',
        'course',
        'target_university_id',
        'target_program_id',
        'applying_program',
        'highest_education',
        'highest_qualification',
        'previous_institution',
        'status',
        'account_status',
        'assigned_to',
        'created_by',
        'notes',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'ssc_year',
        'ssc_result',
        'hsc_year',
        'hsc_result',
        'has_ielts',
        'ielts_score',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'has_ielts' => 'boolean',
    ];

    /**
     * Get the user account associated with this student.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user this student is assigned to.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this student record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all follow-ups for this student.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get all checklist items for this student.
     */
    public function checklists()
    {
        return $this->hasMany(StudentChecklist::class);
    }

    /**
     * Get all documents uploaded for this student.
     */
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * Get the student profile.
     */
    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Get all profile photos.
     */
    public function profilePhotos()
    {
        return $this->hasMany(StudentProfilePhoto::class);
    }

    /**
     * Get the active profile photo.
     */
    public function activeProfilePhoto()
    {
        return $this->hasOne(StudentProfilePhoto::class)->where('is_active', true);
    }

    /**
     * Get the target university.
     */
    public function targetUniversity()
    {
        return $this->belongsTo(University::class, 'target_university_id');
    }

    /**
     * Get the target program.
     */
    public function targetProgram()
    {
        return $this->belongsTo(Program::class, 'target_program_id');
    }

    /**
     * Check if student account is approved.
     */
    public function isApproved(): bool
    {
        return $this->account_status === 'approved';
    }

    /**
     * Check if student account is pending approval.
     */
    public function isPending(): bool
    {
        return $this->account_status === 'pending';
    }

    /**
     * Get checklist completion percentage.
     * Note: Progress now includes both submitted (pending review) and approved documents
     * to provide real-time visibility when students upload documents.
     */
    public function getChecklistProgressAttribute(): array
    {
        $total = $this->checklists()->count();
        if ($total === 0) {
            return [
                'total' => 0,
                'approved' => 0,
                'submitted' => 0,
                'rejected' => 0,
                'pending' => 0,
                'in_progress' => 0, // New: submitted + approved count
                'percentage' => 0,
            ];
        }

        $approved = $this->checklists()
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        $submitted = $this->checklists()
            ->where('status', 'submitted')
            ->count();

        $rejected = $this->checklists()
            ->where('status', 'rejected')
            ->count();

        $pending = $this->checklists()
            ->where('status', 'pending')
            ->count();

        // Calculate in_progress: documents that are submitted or approved
        $inProgress = $approved + $submitted;

        return [
            'total' => $total,
            'approved' => $approved,
            'submitted' => $submitted,
            'rejected' => $rejected,
            'pending' => $pending,
            'in_progress' => $inProgress, // New field for progress tracking
            'percentage' => (int) (($inProgress / $total) * 100), // Changed: now based on submitted + approved
        ];
    }

    /**
     * Get the student's payments.
     */
    public function payments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    /**
     * Get the student's contact submissions.
     */
    public function contactSubmissions()
    {
        return $this->hasMany(ContactSubmission::class);
    }
}
