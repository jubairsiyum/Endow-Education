<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'photo_path',
        'address',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the student profile associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the department this user belongs to (legacy - single department).
     * For backward compatibility. Use departments() for multi-department support.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all departments this user belongs to (many-to-many).
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_user')
                    ->withTimestamps();
    }

    /**
     * Check if user is a manager of any department.
     */
    public function isManagerOfAnyDepartment(): bool
    {
        return Department::where('manager_id', $this->id)->exists();
    }

    /**
     * Get all departments where this user is the manager.
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    /**
     * Check if user is manager of a specific department.
     */
    public function isManagerOfDepartment($departmentId): bool
    {
        return $this->managedDepartments()->where('id', $departmentId)->exists();
    }

    /**
     * Get all daily reports submitted by this user.
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'submitted_by');
    }

    /**
     * Get all students assigned to this user.
     */
    public function assignedStudents()
    {
        return $this->hasMany(Student::class, 'assigned_to');
    }

    /**
     * Get all students created by this user.
     */
    public function createdStudents()
    {
        return $this->hasMany(Student::class, 'created_by');
    }

    /**
     * Get all follow-ups created by this user.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class, 'created_by');
    }

    /**
     * Get all documents uploaded by this user.
     */
    public function uploadedDocuments()
    {
        return $this->hasMany(StudentDocument::class, 'uploaded_by');
    }

    /**
     * Get all student visits handled by this employee.
     */
    public function studentVisits()
    {
        return $this->hasMany(StudentVisit::class, 'employee_id');
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['Super Admin', 'Admin']);
    }

    /**
     * Check if user is Employee.
     */
    public function isEmployee(): bool
    {
        return $this->hasRole('Employee');
    }

    /**
     * Check if user is Student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('Student');
    }
}
