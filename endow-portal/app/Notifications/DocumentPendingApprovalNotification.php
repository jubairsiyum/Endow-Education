<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\StudentChecklist;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentPendingApprovalNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $studentChecklist;

    public function __construct(Student $student, StudentChecklist $studentChecklist)
    {
        $this->student = $student;
        $this->studentChecklist = $studentChecklist;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'document_pending_approval',
            'title' => 'Document Awaiting Approval',
            'message' => $this->student->name . ' submitted document: ' . $this->studentChecklist->checklistItem->name,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'document_name' => $this->studentChecklist->checklistItem->name,
            'checklist_id' => $this->studentChecklist->id,
            'url' => route('students.show', $this->student->id) . '#checklist',
            'icon' => 'fas fa-file-alt',
            'color' => 'warning',
        ];
    }
}
