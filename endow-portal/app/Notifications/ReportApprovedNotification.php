<?php

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Report Approved Notification
 * 
 * Sent to report submitter when report is approved
 */
class ReportApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected DailyReport $report;
    protected ?string $comment;

    public function __construct(DailyReport $report, ?string $comment = null)
    {
        $this->report = $report;
        $this->comment = $comment;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('office.daily-reports.show', $this->report);

        $message = (new MailMessage)
            ->subject('Report Approved - ' . $this->report->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your daily report has been approved!')
            ->line('**Report:** ' . $this->report->title)
            ->line('**Date:** ' . \Carbon\Carbon::parse($this->report->report_date)->format('M d, Y'))
            ->line('**Approved by:** ' . $this->report->approvedBy?->name);

        if ($this->comment) {
            $message->line('**Comments:**')
                   ->line($this->comment);
        }

        return $message->action('View Report', $url)
                      ->line('Great work! Keep up the good reporting.');
    }

    public function toArray($notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'type' => 'report_approved',
            'message' => 'Your report has been approved: ' . $this->report->title,
            'url' => route('office.daily-reports.show', $this->report),
        ];
    }
}
