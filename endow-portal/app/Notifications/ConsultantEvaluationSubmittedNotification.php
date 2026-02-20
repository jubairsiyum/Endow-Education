<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultantEvaluationSubmittedNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $consultant;

    public function __construct(Student $student, User $consultant)
    {
        $this->student = $student;
        $this->consultant = $consultant;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'consultant_evaluation_submitted',
            'title' => 'New Evaluation Received',
            'message' => $this->student->name . ' submitted an evaluation for ' . $this->consultant->name,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'consultant_id' => $this->consultant->id,
            'consultant_name' => $this->consultant->name,
            'url' => route('admin.consultant-evaluations.show', $this->consultant->id),
            'icon' => 'fas fa-star',
            'color' => 'info',
        ];
    }
}
