<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\StudentProfilePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentProfileService
{
    /**
     * Create a new student with profile.
     */
    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Generate registration ID
            $data['registration_id'] = $this->generateRegistrationId();
            
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Create student
            $student = Student::create([
                'user_id' => $data['user_id'] ?? null,
                'registration_id' => $data['registration_id'],
                'name' => $data['name'],
                'surname' => $data['surname'] ?? null,
                'given_names' => $data['given_names'] ?? null,
                'email' => $data['email'],
                'password' => $data['password'] ?? null,
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'country' => $data['country'],
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'status' => $data['status'] ?? 'new',
                'account_status' => $data['account_status'] ?? 'pending',
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => $data['created_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create profile if profile data is provided
            if (isset($data['profile'])) {
                $this->createProfile($student, $data['profile']);
            }

            return $student->load(['profile', 'activeProfilePhoto']);
        });
    }

    /**
     * Update student information.
     */
    public function updateStudent(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            // Update student basic info
            $studentData = array_intersect_key($data, array_flip([
                'name', 'surname', 'given_names', 'email', 'phone', 
                'date_of_birth', 'gender', 'nationality', 'country', 
                'address', 'city', 'postal_code', 'status', 
                'account_status', 'notes', 'father_name', 'mother_name',
                'passport_number', 'passport_expiry_date', 'emergency_contact_name',
                'emergency_contact_phone', 'emergency_contact_relationship'
            ]));

            $student->update($studentData);

            // Update or create profile
            if (isset($data['profile'])) {
                $this->updateOrCreateProfile($student, $data['profile']);
            }

            return $student->fresh(['profile', 'activeProfilePhoto']);
        });
    }

    /**
     * Create student profile.
     */
    public function createProfile(Student $student, array $profileData): StudentProfile
    {
        return $student->profile()->create($profileData);
    }

    /**
     * Update or create student profile.
     */
    public function updateOrCreateProfile(Student $student, array $profileData): StudentProfile
    {
        return $student->profile()->updateOrCreate(
            ['student_id' => $student->id],
            $profileData
        );
    }

    /**
     * Delete student (soft delete).
     */
    public function deleteStudent(Student $student): bool
    {
        return $student->delete();
    }

    /**
     * Restore soft deleted student.
     */
    public function restoreStudent(int $studentId): bool
    {
        $student = Student::withTrashed()->findOrFail($studentId);
        return $student->restore();
    }

    /**
     * Permanently delete student.
     */
    public function forceDeleteStudent(Student $student): bool
    {
        return DB::transaction(function () use ($student) {
            // Delete profile photos
            $student->profilePhotos()->each(function ($photo) {
                $photo->delete();
            });

            // Force delete student
            return $student->forceDelete();
        });
    }

    /**
     * Generate unique registration ID.
     */
    protected function generateRegistrationId(): string
    {
        $prefix = 'STU';
        $year = date('Y');
        
        do {
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $registrationId = "{$prefix}{$year}{$random}";
        } while (Student::where('registration_id', $registrationId)->exists());

        return $registrationId;
    }

    /**
     * Get student with all relations.
     */
    public function getStudentWithRelations(int $studentId): Student
    {
        return Student::with([
            'profile',
            'activeProfilePhoto',
            'profilePhotos',
            'user',
            'assignedUser',
            'creator',
            'targetUniversity',
            'targetProgram'
        ])->findOrFail($studentId);
    }

    /**
     * Search students.
     */
    public function searchStudents(array $filters = [])
    {
        $query = Student::with(['profile', 'activeProfilePhoto']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('registration_id', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['account_status'])) {
            $query->where('account_status', $filters['account_status']);
        }

        if (isset($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Update student status.
     */
    public function updateStatus(Student $student, string $status): Student
    {
        $student->update(['status' => $status]);
        return $student;
    }

    /**
     * Approve student account.
     */
    public function approveStudent(Student $student): Student
    {
        $student->update(['account_status' => 'approved']);
        return $student;
    }

    /**
     * Reject student account.
     */
    public function rejectStudent(Student $student, string $reason = null): Student
    {
        $student->update([
            'account_status' => 'rejected',
            'notes' => $reason ? ($student->notes ? $student->notes . "\n\n" . $reason : $reason) : $student->notes
        ]);
        return $student;
    }
}
