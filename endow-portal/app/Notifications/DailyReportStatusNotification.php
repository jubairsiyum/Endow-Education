<?php

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailyReportStatusNotification extends Notification
{
    use Queueable;

    protected $report;
    protected $status;
    protected $comment;

    public function __construct(DailyReport $report, string $status, ?string $comment = null)
    {
        $this->report = $report;
        $this->status = $status;
        $this->comment = $comment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $statusText = ucfirst($this->status);
        $color = $this->status === 'approved' ? 'success' : 'danger';
        $icon = $this->status === 'approved' ? 'fas fa-check-circle' : 'fas fa-times-circle';

        $message = 'Your daily report "' . $this->report->title . '" has been ' . strtolower($statusText);
        
        if ($this->comment) {
            $message .= '. Comment: ' . $this->comment;
        }

        return [
            'type' => 'daily_report_' . $this->status,
            'title' => 'Report ' . $statusText,
            'message' => $message,
            'report_id' => $this->report->id,
            'report_title' => $this->report->title,
            'status' => $this->status,
            'comment' => $this->comment,
            'url' => route('office.daily-reports.show', $this->report->id),
            'icon' => $icon,
            'color' => $color,
        ];
    }
}
