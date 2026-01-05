<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, User $assignedBy)
    {
        $this->student = $student;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Student Assigned to You - Endow Connect')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have been assigned a new student by ' . $this->assignedBy->name . '.')
            ->line('')
            ->line('**Student Details:**')
            ->line('Name: ' . $this->student->name)
            ->line('Email: ' . $this->student->email)
            ->line('Phone: ' . $this->student->phone)
            ->line('Country: ' . $this->student->country)
            ->line('University: ' . ($this->student->targetUniversity->name ?? 'Not selected'))
            ->line('Program: ' . ($this->student->targetProgram->name ?? 'Not selected'))
            ->line('Application Status: ' . ucfirst($this->student->status))
            ->line('Account Status: ' . ucfirst($this->student->account_status))
            ->line('')
            ->line('Please review the student\'s profile and take appropriate action.')
            ->action('View Student Profile', route('students.show', $this->student->id))
            ->line('You can manage all your assigned students from the "My Students" section.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'assigned_by' => $this->assignedBy->name,
            'assigned_at' => now()->toDateTimeString(),
        ];
    }
}
