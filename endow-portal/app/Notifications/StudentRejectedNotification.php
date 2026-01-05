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
            ->subject('Application Status Update - Endow Connect')
            ->greeting('Dear ' . $this->student->name . ',')
            ->line('Thank you for your interest in Endow Connect and for taking the time to submit your application.')
            ->line('')
            ->line('After careful review, we regret to inform you that your application has not been approved at this time.')
            ->line('');

        if ($this->reason) {
            $message->line('**Reason for Rejection:**')
                ->line($this->reason)
                ->line('');
        }

        $message->line('**What\'s Next?**')
            ->line('• You may reapply in the future if your circumstances change')
            ->line('• If you believe this decision was made in error, please contact us')
            ->line('• Feel free to reach out if you need clarification or have questions')
            ->line('')
            ->line('**Contact Information:**')
            ->line('If you would like to discuss your application or need further information, please don\'t hesitate to contact our support team.')
            ->line('')
            ->line('We appreciate your understanding and wish you all the best in your educational endeavors.')
            ->salutation('Best regards, The Endow Connect Team');

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
