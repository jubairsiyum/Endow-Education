<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocument extends Model
{
    use HasFactory, SoftDeletes;

    // Document Type Constants - Attestation Documents
    const TYPE_SSC_CERTIFICATE = 'ssc_certificate';
    const TYPE_SSC_TRANSCRIPT = 'ssc_transcript';
    const TYPE_HSC_CERTIFICATE = 'hsc_certificate';
    const TYPE_HSC_TRANSCRIPT = 'hsc_transcript';
    const TYPE_HONORS_CERTIFICATE = 'honors_certificate';
    const TYPE_HONORS_TRANSCRIPT = 'honors_transcript';
    const TYPE_IELTS_CERTIFICATE = 'ielts_certificate';
    const TYPE_FAMILY_RELATIONSHIP_CERT = 'family_relationship_certificate';
    const TYPE_PASSPORT_COPY = 'passport_copy';
    const TYPE_BIRTH_CERTIFICATE = 'birth_certificate';
    const TYPE_APPLICANT_PHOTO = 'applicant_photo';
    const TYPE_PARENTS_NOC = 'parents_noc';
    const TYPE_TB_TEST = 'tb_test';
    
    // Translation Documents
    const TYPE_NID_FATHER = 'nid_father';
    const TYPE_NID_MOTHER = 'nid_mother';
    const TYPE_NID_OWN = 'nid_own';
    const TYPE_TRADE_LICENSE = 'trade_license';
    const TYPE_EMPLOYMENT_CERT = 'employment_certificate';
    const TYPE_TIN_CERTIFICATE = 'tin_certificate';

    // Document Categories
    const CATEGORY_ATTESTATION = 'attestation';
    const CATEGORY_TRANSLATION = 'translation';

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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
        'document_category',
        'copy_number',
        'attestation_details',
        'is_notarized',
        'is_translated',
        'filename',
        'original_name',
        'file_name',
        'file_path',
        'title',
        'file_type',
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
        'is_notarized' => 'boolean',
        'is_translated' => 'boolean',
        'attestation_details' => 'array',
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
