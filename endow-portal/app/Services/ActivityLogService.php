<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity
     *
     * @param string $logName
     * @param string $description
     * @param mixed $subject
     * @param array $properties
     * @return ActivityLog
     */
    public function log(
        string $logName,
        string $description,
        $subject = null,
        array $properties = []
    ): ActivityLog {
        $log = new ActivityLog();
        $log->log_name = $logName;
        $log->description = $description;
        $log->properties = $properties;
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();

        if ($subject) {
            $log->subject_type = get_class($subject);
            $log->subject_id = $subject->id;
        }

        if (Auth::check()) {
            $log->causer_type = get_class(Auth::user());
            $log->causer_id = Auth::id();
        }

        $log->save();

        return $log;
    }

    /**
     * Log student creation
     *
     * @param \App\Models\Student $student
     * @return ActivityLog
     */
    public function logStudentCreated($student): ActivityLog
    {
        return $this->log(
            'student',
            'Student record created',
            $student,
            [
                'name' => $student->name,
                'email' => $student->email,
                'status' => $student->status,
            ]
        );
    }

    /**
     * Log student assignment change
     *
     * @param \App\Models\Student $student
     * @param int|null $oldAssignedTo
     * @param int|null $newAssignedTo
     * @return ActivityLog
     */
    public function logStudentAssigned($student, $oldAssignedTo, $newAssignedTo): ActivityLog
    {
        return $this->log(
            'student',
            'Student assignment changed',
            $student,
            [
                'old_assigned_to' => $oldAssignedTo,
                'new_assigned_to' => $newAssignedTo,
            ]
        );
    }

    /**
     * Log document approval
     *
     * @param \App\Models\StudentDocument $document
     * @return ActivityLog
     */
    public function logDocumentApproved($document): ActivityLog
    {
        return $this->log(
            'document',
            'Document approved',
            $document,
            [
                'filename' => $document->filename,
                'student_id' => $document->student_id,
            ]
        );
    }

    /**
     * Log document rejection
     *
     * @param \App\Models\StudentDocument $document
     * @param string $reason
     * @return ActivityLog
     */
    public function logDocumentRejected($document, string $reason = ''): ActivityLog
    {
        return $this->log(
            'document',
            'Document rejected',
            $document,
            [
                'filename' => $document->filename,
                'student_id' => $document->student_id,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Log student account approval
     *
     * @param \App\Models\Student $student
     * @return ActivityLog
     */
    public function logStudentApproved($student): ActivityLog
    {
        return $this->log(
            'student',
            'Student account approved',
            $student,
            [
                'name' => $student->name,
                'email' => $student->email,
            ]
        );
    }

    /**
     * Log student account rejection
     *
     * @param \App\Models\Student $student
     * @param string $reason
     * @return ActivityLog
     */
    public function logStudentRejected($student, string $reason = ''): ActivityLog
    {
        return $this->log(
            'student',
            'Student account rejected',
            $student,
            [
                'name' => $student->name,
                'email' => $student->email,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Log student login
     *
     * @param \App\Models\Student $student
     * @return ActivityLog
     */
    public function logStudentLogin($student): ActivityLog
    {
        return $this->log(
            'authentication',
            'Student logged in',
            $student,
            [
                'student_id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ]
        );
    }

    /**
     * Log document upload
     *
     * @param \App\Models\StudentDocument $document
     * @return ActivityLog
     */
    public function logDocumentUploaded($document): ActivityLog
    {
        return $this->log(
            'document',
            'Document uploaded',
            $document,
            [
                'student_id' => $document->student_id,
                'checklist_item_id' => $document->checklist_item_id,
                'filename' => $document->filename,
                'original_name' => $document->original_name,
            ]
        );
    }

    /**
     * Log document deletion
     *
     * @param \App\Models\StudentDocument $document
     * @return ActivityLog
     */
    public function logDocumentDeleted($document): ActivityLog
    {
        return $this->log(
            'document',
            'Document deleted',
            $document,
            [
                'student_id' => $document->student_id,
                'checklist_item_id' => $document->checklist_item_id,
                'filename' => $document->filename,
                'original_name' => $document->original_name,
            ]
        );
    }

    /**
     * Log student profile update
     *
     * @param \App\Models\Student $student
     * @param array $changes
     * @return ActivityLog
     */
    public function logStudentUpdated($student, array $changes = []): ActivityLog
    {
        return $this->log(
            'student',
            'Student profile updated',
            $student,
            [
                'student_id' => $student->id,
                'changes' => $changes,
            ]
        );
    }

    /**
     * Log checklist access
     *
     * @param \App\Models\Student $student
     * @return ActivityLog
     */
    public function logChecklistAccessed($student): ActivityLog
    {
        return $this->log(
            'checklist',
            'Student accessed checklist',
            $student,
            [
                'student_id' => $student->id,
            ]
        );
    }
}
