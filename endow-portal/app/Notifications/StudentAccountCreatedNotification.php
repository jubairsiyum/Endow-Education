<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentAccountCreatedNotification extends Notification
{
    use Queueable;

    public $student;
    public $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, string $temporaryPassword)
    {
        $this->student = $student;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $loginUrl = url('/student/login');

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Your Account Details')
            ->greeting('Hello ' . $this->student->name . '!')
            ->line('Your student account has been created successfully at ' . config('app.name') . '.')
            ->line('You can now log in to your student portal using the credentials below:')
            ->line(' ')
            ->line('Email: ' . $this->student->email)
            ->line('Temporary Password: **' . $this->temporaryPassword . '**')
            ->line(' ')
            ->line('IMPORTANT SECURITY NOTICE:')
            ->line('This is a temporary password generated for your first login. For your security, please change this password immediately after logging in. You can change your password from your account settings or use the "Forgot Password" option.')
            ->line(' ')
            ->action('Login to Student Portal', $loginUrl)
            ->line('If you have any questions or need assistance, please contact our support team.')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'student_email' => $this->student->email,
        ];
    }
}
