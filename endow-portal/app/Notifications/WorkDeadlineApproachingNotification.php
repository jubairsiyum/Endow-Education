<?php

namespace App\Notifications;

use App\Models\WorkAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkDeadlineApproachingNotification extends Notification
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
        $dueDate = \Carbon\Carbon::parse($this->workAssignment->due_date);
        $daysRemaining = now()->diffInDays($dueDate, false);

        return [
            'type' => 'work_deadline_approaching',
            'title' => 'Deadline Approaching',
            'message' => 'Work assignment "' . $this->workAssignment->title . '" is due in ' . abs($daysRemaining) . ' day(s)',
            'work_assignment_id' => $this->workAssignment->id,
            'work_title' => $this->workAssignment->title,
            'due_date' => $dueDate->format('M d, Y'),
            'days_remaining' => $daysRemaining,
            'priority' => $this->workAssignment->priority,
            'url' => route('office.work-assignments.show', $this->workAssignment->id),
            'icon' => 'fas fa-clock',
            'color' => 'warning',
        ];
    }
}
