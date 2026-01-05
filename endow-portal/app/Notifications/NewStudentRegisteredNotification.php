<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudentRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
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
            ->subject('🔔 New Student Registration - Action Required')
            ->theme('endow')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new student has registered on the Endow Connect platform and requires your review.')
            ->line('')
            ->line('**Student Details:**')
            ->line('👤 **Name:** ' . $this->student->name)
            ->line('📧 **Email:** ' . $this->student->email)
            ->line('📱 **Phone:** ' . $this->student->phone)
            ->line('🌍 **Country:** ' . $this->student->country)
            ->line('🎓 **University:** ' . ($this->student->targetUniversity->name ?? 'Not selected'))
            ->line('📚 **Program:** ' . ($this->student->targetProgram->name ?? 'Not selected'))
            ->line('')
            ->line('Please review the student\'s application and approve or reject their account.')
            ->action('Review Student Application', route('students.show', $this->student->id))
            ->line('')
            ->line('Thank you for your prompt attention to this matter!');
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
            'student_email' => $this->student->email,
            'registered_at' => $this->student->created_at->toDateTimeString(),
        ];
    }
}
