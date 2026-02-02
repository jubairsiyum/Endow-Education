<?php

namespace App\Notifications;

use App\Models\DailyReportApprover;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Approval Request Notification
 * 
 * Sent to approvers when they need to review/approve a report
 */
class ApprovalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected DailyReportApprover $approver;

    public function __construct(DailyReportApprover $approver)
    {
        $this->approver = $approver;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $report = $this->approver->dailyReport;
        $url = route('office.daily-reports.show', $report);

        return (new MailMessage)
            ->subject('Approval Required - ' . $report->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned to approve a daily report.')
            ->line('**Report:** ' . $report->title)
            ->line('**Department:** ' . $report->department_name)
            ->line('**Submitted by:** ' . $report->submittedBy->name)
            ->line('**Date:** ' . $report->report_date->format('M d, Y'))
            ->line('**Priority:** ' . ucfirst($report->priority))
            ->line('**Approval Level:** ' . $this->approver->approval_level)
            ->action('Review & Approve', $url)
            ->line('Please review and provide your approval decision.');
    }

    public function toArray($notifiable): array
    {
        $report = $this->approver->dailyReport;
        
        return [
            'report_id' => $report->id,
            'approver_id' => $this->approver->id,
            'title' => $report->title,
            'type' => 'approval_request',
            'message' => 'Approval needed: ' . $report->title,
            'url' => route('office.daily-reports.show', $report),
        ];
    }
}
