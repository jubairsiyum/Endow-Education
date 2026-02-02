<?php

namespace App\Notifications;

use App\Models\StudentChecklist;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $studentChecklist;
    protected $student;
    protected $feedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(StudentChecklist $studentChecklist, Student $student, string $feedback)
    {
        $this->studentChecklist = $studentChecklist;
        $this->student = $student;
        $this->feedback = $feedback;
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
        $documentTitle = $this->studentChecklist->checklistItem->title ?? 'Document';
        $portalUrl = url('/student/documents');

        return (new MailMessage)
            ->subject('📄 Document Review - Revision Needed - ' . config('app.name'))
            ->theme('endow')
            ->greeting('Hello ' . $this->student->name . '!')
            ->line('We have reviewed your submitted document and it requires some revisions before it can be approved.')
            ->line('')
            ->line('**Document Details:**')
            ->line('📋 **Document Name:** ' . $documentTitle)
            ->line('📅 **Reviewed On:** ' . now()->format('F d, Y \a\t h:i A'))
            ->line('')
            ->line('**Feedback from Reviewer:**')
            ->line('💬 ' . $this->feedback)
            ->line('')
            ->line('**Next Steps:**')
            ->line('1. Review the feedback carefully')
            ->line('2. Make necessary corrections to your document')
            ->line('3. Resubmit the corrected document through your student portal')
            ->line('')
            ->action('Resubmit Document', $portalUrl)
            ->line('')
            ->line('**Important Notes:**')
            ->line('• Please address all the points mentioned in the feedback')
            ->line('• Ensure your document is clear, legible, and meets the requirements')
            ->line('• Use PDF format when possible for best quality')
            ->line('• Keep file size under 10MB')
            ->line('')
            ->line('If you have any questions about the feedback or need assistance, please don\'t hesitate to contact your assigned counselor or our support team.')
            ->line('')
            ->salutation('Best regards,
' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'student_checklist_id' => $this->studentChecklist->id,
            'student_id' => $this->student->id,
            'document_title' => $this->studentChecklist->checklistItem->title ?? 'Document',
            'feedback' => $this->feedback,
        ];
    }
}
