<?php

namespace App\Notifications;

use App\Models\WorkAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkFeedbackProvidedNotification extends Notification
{
    use Queueable;

    protected $workAssignment;

    public function __construct(WorkAssignment $workAssignment)
    {
        $this->workAssignment = $workAssignment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'work_feedback_provided',
            'title' => 'Manager Feedback Received',
            'message' => 'Your manager provided feedback on: ' . $this->workAssignment->title,
            'work_assignment_id' => $this->workAssignment->id,
            'work_title' => $this->workAssignment->title,
            'feedback_by' => $this->workAssignment->assignedBy->name,
            'url' => route('office.work-assignments.show', $this->workAssignment->id),
            'icon' => 'fas fa-comments',
            'color' => 'info',
        ];
    }
}
