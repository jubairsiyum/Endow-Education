<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, $reason = '')
    {
        $this->student = $student;
        $this->reason = $reason;
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
        $message = (new MailMessage)
            ->subject('Application Status Update - Endow Education')
            ->greeting('Hello ' . $this->student->name . ',')
            ->line('Thank you for your interest in Endow Education.')
            ->line('We regret to inform you that your application has not been approved at this time.');

        if ($this->reason) {
            $message->line('Reason: ' . $this->reason);
        }

        $message->line('If you believe this is an error or would like to discuss your application, please contact us.')
            ->line('We wish you the best in your future endeavors.');

        return $message;
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
            'reason' => $this->reason,
        ];
    }
}
