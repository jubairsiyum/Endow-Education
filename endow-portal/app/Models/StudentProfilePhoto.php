<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentProfilePhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'photo_path',
        'thumbnail_path',
        'original_filename',
        'mime_type',
        'file_size',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the photo.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the full URL for the photo.
     */
    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo_path) {
            return asset('images/default-avatar.png');
        }
        return url('storage/' . $this->photo_path);
    }

    /**
     * Get the full URL for the thumbnail.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        return url('storage/' . $this->thumbnail_path);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete the photo file from storage.
     */
    public function deleteFile(): bool
    {
        $deleted = true;
        
        if (Storage::disk('public')->exists($this->photo_path)) {
            $deleted = Storage::disk('public')->delete($this->photo_path);
        }
        
        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            Storage::disk('public')->delete($this->thumbnail_path);
        }
        
        return $deleted;
    }

    /**
     * Deactivate all other photos for this student.
     */
    public function deactivateOthers(): void
    {
        self::where('student_id', $this->student_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting a photo, delete the file from storage
        static::deleting(function ($photo) {
            $photo->deleteFile();
        });
    }
}
