<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentProfileRequest;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UploadProfilePhotoRequest;
use App\Models\Student;
use App\Services\ImageProcessingService;
use App\Services\StudentProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProfileApiController extends Controller
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
     * Display a listing of students.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'account_status' => $request->input('account_status'),
            'gender' => $request->input('gender'),
            'per_page' => $request->input('per_page', 15),
        ];

        $students = $this->profileService->searchStudents($filters);

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentProfileRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;

            $student = $this->profileService->createStudent($data);

            return response()->json([
                'success' => true,
                'message' => 'Student profile created successfully!',
                'data' => $student
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student profile',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $student = $this->profileService->getStudentWithRelations($student->id);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentProfileRequest $request, Student $student): JsonResponse
    {
        $this->authorize('update', $student);

        try {
            $student = $this->profileService->updateStudent($student, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Student profile updated successfully!',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student profile',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->authorize('delete', $student);

        try {
            $this->profileService->deleteStudent($student);

            return response()->json([
                'success' => true,
                'message' => 'Student profile deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student profile',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(UploadProfilePhotoRequest $request, Student $student): JsonResponse
    {
        $this->authorize('update', $student);

        try {
            $photo = $this->imageService->uploadProfilePhoto($student, $request->file('photo'));

            return response()->json([
                'success' => true,
                'message' => 'Profile photo uploaded successfully!',
                'data' => [
                    'photo_url' => $photo->photo_url,
                    'thumbnail_url' => $photo->thumbnail_url,
                    'photo_id' => $photo->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete profile photo.
     */
    public function deletePhoto(Student $student): JsonResponse
    {
        $this->authorize('update', $student);

        try {
            $photo = $student->activeProfilePhoto;
            
            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active profile photo found.'
                ], 404);
            }

            $this->imageService->deleteProfilePhoto($photo);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
