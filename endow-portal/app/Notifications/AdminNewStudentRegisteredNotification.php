<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewStudentRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected const NOT_PROVIDED = 'Not provided';

    protected $student;
    protected $admin;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, User $admin)
    {
        $this->student = $student;
        $this->admin = $admin;
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
            ->subject('🆕 New Student Registration - Pending Review')
            ->theme('endow')
            ->greeting('Hello ' . $notifiable->name . '! 👋')
            ->line('A new student has registered on the **Endow Connect** platform and is awaiting your review and approval.')
            ->line('')
            ->line('**📋 Student Registration Details:**')
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('👤 **Name:** ' . $this->student->name)
            ->line('📧 **Email Address:** ' . $this->student->email)
            ->line('📱 **Phone Number:** ' . ($this->student->phone ?? self::NOT_PROVIDED))
            ->line('🌍 **Country:** ' . ($this->student->country ?? self::NOT_PROVIDED))
            ->line('👶 **Date of Birth:** ' . ($this->student->date_of_birth ? $this->student->date_of_birth->format('M d, Y') : self::NOT_PROVIDED))
            ->line('⚧️ **Gender:** ' . ($this->student->gender ?? 'Not specified'))
            ->line('📅 **Registered On:** ' . $this->student->created_at->format('F d, Y \a\t g:i A'))
            ->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━')
            ->line('')
            ->line('**📊 Current Status:**')
            ->line('• Account Status: **Pending Approval**')
            ->line('• Registration Status: **New**')
            ->line('')
            ->line('**🎯 Next Steps:**')
            ->line('1. Review the student\'s application details')
            ->line('2. Verify the provided information')
            ->line('3. Assign a target university and program (if applicable)')
            ->line('4. Assign the student to a counselor')
            ->line('5. Approve or reject the application')
            ->line('')
            ->action('Review Student Application', route('students.show', $this->student->id))
            ->line('')
            ->line('**ℹ️ Quick Reference:**')
            ->line('• Student Profile: ' . route('students.show', $this->student->id))
            ->line('• View All Pending: ' . route('students.index') . '?account_status=pending')
            ->line('')
            ->line('If you have any questions or need additional information about this student, please review their full profile.')
            ->line('')
            ->line('Thank you for your attention!')
            ->line('— Endow Connect Administration Team');
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
            'student_phone' => $this->student->phone,
            'registered_at' => $this->student->created_at->toDateTimeString(),
            'admin_id' => $notifiable->id,
        ];
    }
}
