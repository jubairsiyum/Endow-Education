<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentProfilePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    /**
     * Convert image (PNG/JPG) to PDF format for document viewing.
     * Returns array with PDF file data and metadata.
     *
     * @param UploadedFile $file The image file to convert
     * @param string|null $originalFilename Original filename to preserve
     * @return array ['content' => base64_encoded_pdf, 'filename' => new_filename, 'mime_type' => 'application/pdf', 'size' => file_size]
     */
    public function convertImageToPdf(UploadedFile $file, ?string $originalFilename = null): array
    {
        // Get original filename or use provided one
        $originalFilename = $originalFilename ?? $file->getClientOriginalName();

        // Check if file is an image
        $mimeType = $file->getMimeType();
        $isImage = in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png']);

        if (!$isImage) {
            throw new \Exception('File must be an image (JPG, JPEG, or PNG)');
        }

        // Read image and convert to base64
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $imageSrc = "data:{$mimeType};base64,{$imageData}";

        // Get image dimensions
        list($width, $height) = getimagesize($file->getRealPath());

        // Calculate PDF dimensions (A4 page with margins)
        $maxWidth = 550; // A4 width with margins
        $maxHeight = 750; // A4 height with margins

        // Calculate scaled dimensions maintaining aspect ratio
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $scaledWidth = $width * $ratio;
        $scaledHeight = $height * $ratio;

        // Create HTML for PDF with centered image
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body {
                    margin: 0;
                    padding: 20px;
                    font-family: Arial, sans-serif;
                }
                .container {
                    text-align: center;
                    page-break-inside: avoid;
                }
                .document-title {
                    font-size: 12px;
                    color: #666;
                    margin-bottom: 15px;
                    font-weight: normal;
                }
                .image-container {
                    display: inline-block;
                    max-width: 100%;
                }
                img {
                    max-width: 100%;
                    height: auto;
                    border: 1px solid #ddd;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='image-container'>
                    <img src='{$imageSrc}' style='width: {$scaledWidth}px; height: {$scaledHeight}px;' />
                </div>
            </div>
        </body>
        </html>
        ";

        // Configure DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Create PDF
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Get PDF output
        $pdfContent = $dompdf->output();
        $base64Pdf = base64_encode($pdfContent);

        // Generate new filename with .pdf extension
        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '.pdf';

        return [
            'content' => $base64Pdf,
            'filename' => $newFilename,
            'mime_type' => 'application/pdf',
            'size' => strlen($pdfContent),
            'original_filename' => $originalFilename,
            'converted_from' => $mimeType,
        ];
    }

    /**
     * Check if file should be converted to PDF (is it an image?)
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function shouldConvertToPdf(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png']);
    }

    /**
     * Convert image to PDF from in-memory content to avoid file read errors.
     * This method prevents err_upload_file_changed errors by working with content already in memory.
     *
     * @param string $fileContent The file content in binary
     * @param string $originalFilename Original filename to preserve
     * @param string $mimeType The MIME type of the file
     * @return array ['content' => base64_encoded_pdf, 'filename' => new_filename, 'mime_type' => 'application/pdf', 'size' => file_size]
     */
    public function convertImageToPdfFromContent(string $fileContent, string $originalFilename, string $mimeType): array
    {
        // Validate it's an image type
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new \Exception('File must be an image (JPG, JPEG, or PNG)');
        }

        try {
            // Save content to a temporary file for image processing
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tempFile, $fileContent);

            // Verify the temporary file was created
            if (!file_exists($tempFile)) {
                throw new \Exception('Failed to create temporary file for image processing');
            }

            // Get image dimensions from the temporary file
            $imageInfo = @getimagesize($tempFile);
            if ($imageInfo === false) {
                unlink($tempFile);
                throw new \Exception('Invalid or corrupted image file');
            }

            list($width, $height) = $imageInfo;

            // Convert content to base64 for embedding in HTML
            $imageData = base64_encode($fileContent);
            $imageSrc = "data:{$mimeType};base64,{$imageData}";

            // Calculate PDF dimensions (A4 page with margins)
            $maxWidth = 550; // A4 width with margins
            $maxHeight = 750; // A4 height with margins

            // Calculate scaled dimensions maintaining aspect ratio
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $scaledWidth = $width * $ratio;
            $scaledHeight = $height * $ratio;

            // Create HTML for PDF with centered image
            $html = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <style>
                    body {
                        margin: 0;
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .container {
                        text-align: center;
                        page-break-inside: avoid;
                    }
                    .image-container {
                        display: inline-block;
                        max-width: 100%;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                        border: 1px solid #ddd;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='image-container'>
                        <img src='{$imageSrc}' style='width: {$scaledWidth}px; height: {$scaledHeight}px;' />
                    </div>
                </div>
            </body>
            </html>
            ";

            // Configure DomPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('chroot', sys_get_temp_dir());

            // Create PDF
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Get PDF output
            $pdfContent = $dompdf->output();

            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            // Verify PDF was created successfully
            if (empty($pdfContent)) {
                throw new \Exception('Failed to generate PDF content');
            }

            $base64Pdf = base64_encode($pdfContent);

            // Generate new filename with .pdf extension
            $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '.pdf';

            return [
                'content' => $base64Pdf,
                'filename' => $newFilename,
                'mime_type' => 'application/pdf',
                'size' => strlen($pdfContent),
                'original_filename' => $originalFilename,
                'converted_from' => $mimeType,
            ];

        } catch (\Exception $e) {
            // Clean up temporary file if it exists
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            throw new \Exception('Image to PDF conversion failed: ' . $e->getMessage());
        }
    }
}
