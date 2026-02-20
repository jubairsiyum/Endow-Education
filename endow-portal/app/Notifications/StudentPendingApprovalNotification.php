<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentPendingApprovalNotification extends Notification
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
            'type' => 'student_pending_approval',
            'title' => 'Student Pending Approval',
            'message' => 'New student registration awaiting approval: ' . $this->student->name,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'student_email' => $this->student->email,
            'url' => route('students.show', $this->student->id),
            'icon' => 'fas fa-user-clock',
            'color' => 'warning',
        ];
    }
}
