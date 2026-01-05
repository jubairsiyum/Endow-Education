<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentProfileRequest;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UploadProfilePhotoRequest;
use App\Models\Student;
use App\Services\ImageProcessingService;
use App\Services\StudentProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StudentProfileController extends Controller
{
    protected StudentProfileService $profileService;
    protected ImageProcessingService $imageService;

    public function __construct(
        StudentProfileService $profileService,
        ImageProcessingService $imageService
    ) {
        $this->profileService = $profileService;
        $this->imageService = $imageService;
    }

    /**
     * Display the student's profile.
     */
    public function show(Request $request, Student $student)
    {
        $this->authorize('view', $student);

        // Check if tables exist before loading relationships
        try {
            if (Schema::hasTable('student_profiles') && Schema::hasTable('student_profile_photos')) {
                $student->load([
                    'profile',
                    'activeProfilePhoto',
                    'targetUniversity',
                    'targetProgram',
                    'user'
                ]);
            } else {
                $student->load(['targetUniversity', 'targetProgram', 'user']);
            }
        } catch (\Exception $e) {
            $student->load(['targetUniversity', 'targetProgram', 'user']);
        }

        return view('student.profile.show', compact('student'));
    }

    /**
     * Show the form for editing the student's profile.
     */
    public function edit(Request $request, Student $student = null)
    {
        // If no student specified, get the authenticated student
        if (!$student) {
            $user = Auth::user();
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                abort(404, 'Student profile not found');
            }
        }

        $this->authorize('update', $student);

        // Check if tables exist before loading relationships
        try {
            if (Schema::hasTable('student_profiles') && Schema::hasTable('student_profile_photos')) {
                // Force refresh to get the latest data
                $student->refresh();
                $student->load(['profile', 'activeProfilePhoto']);
            }
        } catch (\Exception $e) {
            // Tables don't exist yet - migrations need to be run
        }

        return view('student.profile.edit', compact('student'));
    }

    /**
     * Update the student's profile.
     */
    public function update(UpdateStudentProfileRequest $request, Student $student = null)
    {
        // If no student specified, get the authenticated student
        if (!$student) {
            $user = Auth::user();
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                return back()
                    ->withInput()
                    ->with('error', 'Student profile not found');
            }
        }

        $this->authorize('update', $student);

        try {
            $student = $this->profileService->updateStudent($student, $request->validated());

            // Update user model if name or email changed
            if ($student->user) {
                $student->user->update([
                    'name' => $student->name,
                    'email' => $student->email,
                ]);
                // Refresh the authenticated user to update cached data
                Auth::setUser($student->user->fresh());
            }

            return redirect()
                ->back()
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new student profile.
     */
    public function create()
    {
        $this->authorize('create', Student::class);

        return view('student.profile.create');
    }

    /**
     * Store a newly created student profile.
     */
    public function store(StoreStudentProfileRequest $request)
    {
        $this->authorize('create', Student::class);

        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();

            $student = $this->profileService->createStudent($data);

            return redirect()
                ->route('student.profile.show', $student)
                ->with('success', 'Student profile created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create profile: ' . $e->getMessage());
        }
    }

    /**
     * Remove the student profile.
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        try {
            $this->profileService->deleteStudent($student);

            return redirect()
                ->route('dashboard')
                ->with('success', 'Student profile deleted successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete profile: ' . $e->getMessage());
        }
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(UploadProfilePhotoRequest $request, Student $student = null)
    {
        // If no student specified, get the authenticated student
        if (!$student) {
            $user = Auth::user();
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Student profile not found'
                    ], 404);
                }
                return back()->with('error', 'Student profile not found');
            }
        }

        $this->authorize('update', $student);

        try {
            $photo = $this->imageService->uploadProfilePhoto($student, $request->file('photo'));

            // Refresh the student model to ensure the latest relationship is loaded
            $student->refresh();
            $student->load('activeProfilePhoto');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile photo uploaded successfully!',
                    'photo' => [
                        'id' => $photo->id,
                        'url' => $photo->photo_url . '?t=' . time(), // Cache busting
                        'thumbnail_url' => $photo->thumbnail_url ? $photo->thumbnail_url . '?t=' . time() : null,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Profile photo uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Profile photo upload failed: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'file' => $request->file('photo') ? $request->file('photo')->getClientOriginalName() : 'no file',
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload photo: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Failed to upload photo: ' . $e->getMessage());
        }
    }

    /**
     * Delete profile photo.
     */
    public function deletePhoto(Request $request, Student $student = null)
    {
        // If no student specified, get the authenticated student
        if (!$student) {
            $user = Auth::user();
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                return back()->with('error', 'Student profile not found');
            }
        }

        $this->authorize('update', $student);

        try {
            $photo = $student->activeProfilePhoto;
            
            if (!$photo) {
                return back()->with('error', 'No active profile photo found.');
            }

            $this->imageService->deleteProfilePhoto($photo);

            return back()->with('success', 'Profile photo deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Profile photo deletion failed: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to delete photo: ' . $e->getMessage());
        }
    }
}
