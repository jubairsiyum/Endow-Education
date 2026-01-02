<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentProfilePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageProcessingService
{
    /**
     * Upload and process student profile photo.
     */
    public function uploadProfilePhoto(Student $student, UploadedFile $file): StudentProfilePhoto
    {
        return DB::transaction(function () use ($student, $file) {
            // Deactivate existing photos
            $student->profilePhotos()->update(['is_active' => false]);

            // Generate unique filename
            $filename = $this->generateFilename($file);
            $path = "student-photos/{$student->id}";

            // Process and save main photo
            $photoPath = $this->processAndSaveImage($file, $path, $filename, 300, 300);

            // Process and save thumbnail
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = $this->processAndSaveImage($file, $path, $thumbnailFilename, 150, 150);

            // Create photo record
            $photo = StudentProfilePhoto::create([
                'student_id' => $student->id,
                'photo_path' => $photoPath,
                'thumbnail_path' => $thumbnailPath,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_active' => true,
            ]);

            return $photo;
        });
    }

    /**
     * Replace existing profile photo.
     */
    public function replaceProfilePhoto(Student $student, UploadedFile $file): StudentProfilePhoto
    {
        return DB::transaction(function () use ($student, $file) {
            // Delete old photos
            $oldPhotos = $student->profilePhotos;
            foreach ($oldPhotos as $oldPhoto) {
                $oldPhoto->delete();
            }

            // Upload new photo
            return $this->uploadProfilePhoto($student, $file);
        });
    }

    /**
     * Delete profile photo.
     */
    public function deleteProfilePhoto(StudentProfilePhoto $photo): bool
    {
        return $photo->delete();
    }

    /**
     * Process and save image with resizing.
     */
    protected function processAndSaveImage(
        UploadedFile $file,
        string $path,
        string $filename,
        int $width,
        int $height
    ): string {
        // Ensure directory exists
        Storage::disk('public')->makeDirectory($path);

        // Check if Intervention Image is available
        if (class_exists('Intervention\Image\Facades\Image')) {
            return $this->processWithIntervention($file, $path, $filename, $width, $height);
        } else {
            return $this->processWithGD($file, $path, $filename, $width, $height);
        }
    }

    /**
     * Process image using Intervention Image library.
     */
    protected function processWithIntervention(
        UploadedFile $file,
        string $path,
        string $filename,
        int $width,
        int $height
    ): string {
        // Use string-based class resolution to avoid import errors when package not installed
        $imageClass = '\Intervention\Image\Facades\Image';
        $image = $imageClass::make($file);
        
        // Resize image maintaining aspect ratio
        $image->fit($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Optimize image quality
        $image->encode('jpg', 85);

        // Save to storage
        $fullPath = "{$path}/{$filename}";
        Storage::disk('public')->put($fullPath, (string) $image);

        return $fullPath;
    }

    /**
     * Process image using GD library (fallback).
     */
    protected function processWithGD(
        UploadedFile $file,
        string $path,
        string $filename,
        int $width,
        int $height
    ): string {
        // Get image info
        $imageInfo = getimagesize($file->getRealPath());
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate dimensions maintaining aspect ratio
        $ratio = min($width / $sourceWidth, $height / $sourceHeight);
        $newWidth = (int)($sourceWidth * $ratio);
        $newHeight = (int)($sourceHeight * $ratio);

        // Create source image
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($file->getRealPath());
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($file->getRealPath());
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize
        imagecopyresampled(
            $newImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $sourceWidth, $sourceHeight
        );

        // Save to temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'img');
        imagejpeg($newImage, $tempPath, 85);

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        // Store file
        $fullPath = "{$path}/{$filename}";
        Storage::disk('public')->put($fullPath, file_get_contents($tempPath));
        unlink($tempPath);

        return $fullPath;
    }

    /**
     * Generate unique filename.
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid('profile_', true) . '.' . $extension;
    }

    /**
     * Validate uploaded file.
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];

        // Check file size (2MB max)
        if ($file->getSize() > 2 * 1024 * 1024) {
            $errors[] = 'File size must not exceed 2MB';
        }

        // Check mime type
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File must be JPG, JPEG, or PNG format';
        }

        // Check if it's a valid image
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            $errors[] = 'Invalid image file';
        }

        return $errors;
    }

    /**
     * Get storage disk.
     */
    protected function getStorageDisk(): string
    {
        return config('filesystems.default', 'public');
    }
}
