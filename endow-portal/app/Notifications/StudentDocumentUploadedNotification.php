<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\ChecklistItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentDocumentUploadedNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $checklistItem;

    public function __construct(Student $student, ChecklistItem $checklistItem)
    {
        $this->student = $student;
        $this->checklistItem = $checklistItem;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'student_document_uploaded',
            'title' => 'Document Uploaded',
            'message' => $this->student->name . ' uploaded a document: ' . $this->checklistItem->name,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'checklist_item' => $this->checklistItem->name,
            'url' => route('students.show', $this->student->id) . '#checklist',
            'icon' => 'fas fa-file-upload',
            'color' => 'info',
        ];
    }
}
