<?php

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Report Submitted Notification
 * 
 * Sent to managers/reviewers when a report is submitted
 */
class ReportSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected DailyReport $report;

    public function __construct(DailyReport $report)
    {
        $this->report = $report;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('office.daily-reports.show', $this->report);

        return (new MailMessage)
            ->subject('New Daily Report Submitted - ' . $this->report->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new daily report has been submitted for your review.')
            ->line('**Report:** ' . $this->report->title)
            ->line('**Department:** ' . $this->report->department_name)
            ->line('**Submitted by:** ' . $this->report->submittedBy->name)
            ->line('**Date:** ' . \Carbon\Carbon::parse($this->report->report_date)->format('M d, Y'))
            ->line('**Priority:** ' . ucfirst($this->report->priority))
            ->action('View Report', $url)
            ->line('Please review this report at your earliest convenience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'type' => 'report_submitted',
            'message' => 'New report submitted: ' . $this->report->title,
            'url' => route('office.daily-reports.show', $this->report),
        ];
    }
}
