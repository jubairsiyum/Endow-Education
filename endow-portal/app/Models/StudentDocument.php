<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocument extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'checklist_item_id',
        'student_checklist_id',
        'document_type',
        'filename',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_data',
        'uploaded_by',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student this document belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the checklist item this document is for.
     */
    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Get the student checklist this document is for.
     */
    public function studentChecklist()
    {
        return $this->belongsTo(StudentChecklist::class);
    }

    /**
     * Get the user who uploaded this document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the user who reviewed this document.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Decode and get the file content.
     */
    public function getDecodedFileContent(): string
    {
        return base64_decode($this->file_data);
    }
}
