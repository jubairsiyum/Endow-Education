<?php

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Report Rejected Notification
 * 
 * Sent to report submitter when report is rejected
 */
class ReportRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected DailyReport $report;
    protected string $reason;

    public function __construct(DailyReport $report, string $reason)
    {
        $this->report = $report;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('office.daily-reports.show', $this->report);

        return (new MailMessage)
            ->subject('Report Needs Revision - ' . $this->report->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your daily report requires revision before it can be approved.')
            ->line('**Report:** ' . $this->report->title)
            ->line('**Date:** ' . \Carbon\Carbon::parse($this->report->report_date)->format('M d, Y'))
            ->line('**Feedback:**')
            ->line($this->reason)
            ->action('View & Revise Report', $url)
            ->line('Please address the feedback and resubmit the report.');
    }

    public function toArray($notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'type' => 'report_rejected',
            'message' => 'Your report needs revision: ' . $this->report->title,
            'url' => route('office.daily-reports.show', $this->report),
        ];
    }
}
