<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $tempPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, $tempPassword = null)
    {
        $this->student = $student;
        $this->tempPassword = $tempPassword;
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
            ->subject('Welcome to Endow Connect')
            ->greeting('Hello ' . $this->student->name . '!')
            ->line('Congratulations! Your student account has been approved.')
            ->line('You can now access your student portal to manage your application and upload documents.');

        if ($this->tempPassword) {
            $message->line('Your login credentials are:')
                ->line('Email: ' . $this->student->email)
                ->line('Temporary Password: ' . $this->tempPassword)
                ->line('Please change your password after your first login.');
        }

        $message->action('Login to Portal', route('student.login'))
            ->line('If you have any questions, please contact your assigned counselor.')
            ->line('Thank you for choosing Endow Connect!');

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
        ];
    }
}
