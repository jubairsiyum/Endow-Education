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
            ->subject('🎉 Welcome to Endow Connect - Account Approved!')
            ->greeting('Hello ' . $this->student->name . '!')
            ->line('**Congratulations!** Your student account has been approved and is now active.')
            ->line('')
            ->line('You can now access your personalized student portal to:')
            ->line('• Track your application progress')
            ->line('• Upload required documents')
            ->line('• View your checklist items')
            ->line('• Communicate with your assigned counselor')
            ->line('');

        if ($this->tempPassword) {
            $message->line('**Your Login Credentials:**')
                ->line('📧 Email: **' . $this->student->email . '**')
                ->line('🔑 Temporary Password: **' . $this->tempPassword . '**')
                ->line('')
                ->line('⚠️ **Important:** Please change your password immediately after your first login for security purposes.');
        }

        $message->line('')
            ->action('Access Student Portal', url('/login'))
            ->line('');

        if ($this->student->assignedUser) {
            $message->line('**Your Assigned Counselor:**')
                ->line('Name: ' . $this->student->assignedUser->name)
                ->line('Email: ' . $this->student->assignedUser->email)
                ->line('');
        }

        if ($this->student->targetUniversity) {
            $message->line('**Your Target University:**')
                ->line($this->student->targetUniversity->name . ' (' . $this->student->targetUniversity->country . ')')
                ->line('');
        }

        if ($this->student->targetProgram) {
            $message->line('**Your Target Program:**')
                ->line($this->student->targetProgram->name . ' (' . $this->student->targetProgram->level . ')')
                ->line('');
        }

        $message->line('If you have any questions or need assistance, please don\'t hesitate to reach out to your counselor.')
            ->line('')
            ->line('We\'re excited to have you with us on this journey!')
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
        ];
    }
}
