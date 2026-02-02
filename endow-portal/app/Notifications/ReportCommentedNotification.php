<?php

namespace App\Notifications;

use App\Models\DailyReport;
use App\Models\DailyReportComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * New Comment on Report Notification
 * 
 * Sent when someone comments on a report
 */
class ReportCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected DailyReport $report;
    protected DailyReportComment $comment;

    public function __construct(DailyReport $report, DailyReportComment $comment)
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
        $url = route('office.daily-reports.show', $this->report) . '#comments';

        return (new MailMessage)
            ->subject('New Comment on Report - ' . $this->report->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->comment->user->name . ' commented on a report:')
            ->line('**Report:** ' . $this->report->title)
            ->line('**Comment:**')
            ->line('"' . \Str::limit($this->comment->comment, 150) . '"')
            ->action('View Comment', $url)
            ->line('You can reply or view the full discussion.');
    }

    public function toArray($notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'comment_id' => $this->comment->id,
            'title' => $this->report->title,
            'type' => 'report_commented',
            'message' => $this->comment->user->name . ' commented on: ' . $this->report->title,
            'url' => route('office.daily-reports.show', $this->report) . '#comments',
        ];
    }
}
