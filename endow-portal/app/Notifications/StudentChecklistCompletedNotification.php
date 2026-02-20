<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentChecklistCompletedNotification extends Notification
{
    use Queueable;

    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'student_checklist_completed',
            'title' => 'Checklist Completed',
            'message' => $this->student->name . ' has completed all checklist items!',
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'url' => route('students.show', $this->student->id) . '#checklist',
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
        ];
    }
}
