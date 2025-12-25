<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class PdfService
{
    /**
     * Maximum allowed file size in bytes (10 MB)
     */
    const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Compress a PDF file to maximum 10 MB and encode to Base64
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     * @throws Exception
     */
    public function compressAndEncode($file): array
    {
        // Validate file type
        if ($file->getMimeType() !== 'application/pdf') {
            throw new Exception('Only PDF files are allowed.');
        }

        // Get file content
        $fileContent = file_get_contents($file->getRealPath());
        $originalSize = strlen($fileContent);

        // If file is already under the limit, just encode it
        if ($originalSize <= self::MAX_FILE_SIZE) {
            return [
                'success' => true,
                'base64' => base64_encode($fileContent),
                'size' => $originalSize,
                'compressed' => false,
            ];
        }

        // TODO: Implement actual PDF compression using Ghostscript or similar
        // For now, we'll check if the file is within limits
        // In production, you should install and use:
        // - Ghostscript for PDF compression
        // - Or use cloud services like Cloudinary, ImageKit, etc.
        
        throw new Exception(
            'File size exceeds 10 MB limit. Please compress the PDF before uploading. ' .
            'Original size: ' . $this->formatBytes($originalSize)
        );
    }

    /**
     * Decode Base64 string to PDF content
     *
     * @param string $base64
     * @return string
     */
    public function decodeBase64(string $base64): string
    {
        return base64_decode($base64);
    }

    /**
     * Validate if the Base64 string is a valid PDF
     *
     * @param string $base64
     * @return bool
     */
    public function isValidPdfBase64(string $base64): bool
    {
        $decoded = base64_decode($base64, true);
        
        if ($decoded === false) {
            return false;
        }

        // Check for PDF signature
        return strpos($decoded, '%PDF') === 0;
    }

    /**
     * Get file size from Base64 string
     *
     * @param string $base64
     * @return int
     */
    public function getBase64FileSize(string $base64): int
    {
        return strlen(base64_decode($base64));
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    public function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Sanitize filename
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove any path information
        $filename = basename($filename);
        
        // Remove special characters except dots, dashes, and underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Ensure it ends with .pdf
        if (!str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }
        
        return $filename;
    }

    /**
     * Generate download response for Base64 PDF
     *
     * @param string $base64
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function downloadResponse(string $base64, string $filename)
    {
        $content = $this->decodeBase64($base64);
        
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($content));
    }

    /**
     * Generate inline view response for Base64 PDF
     *
     * @param string $base64
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function viewResponse(string $base64, string $filename)
    {
        $content = $this->decodeBase64($base64);
        
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Content-Length', strlen($content));
    }
}
