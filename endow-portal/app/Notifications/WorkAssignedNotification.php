<?php

namespace App\Notifications;

use App\Models\WorkAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkAssignedNotification extends Notification
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
        $dueDate = $this->workAssignment->due_date 
            ? \Carbon\Carbon::parse($this->workAssignment->due_date)->format('M d, Y')
            : 'Not set';

        return [
            'type' => 'work_assigned',
            'title' => 'New Work Assignment',
            'message' => 'You have been assigned: ' . $this->workAssignment->title,
            'work_assignment_id' => $this->workAssignment->id,
            'work_title' => $this->workAssignment->title,
            'priority' => $this->workAssignment->priority,
            'due_date' => $dueDate,
            'assigned_by' => $this->workAssignment->assignedBy->name,
            'url' => route('office.work-assignments.show', $this->workAssignment->id),
            'icon' => 'fas fa-tasks',
            'color' => $this->workAssignment->priority === 'urgent' ? 'danger' : 'primary',
        ];
    }
}
