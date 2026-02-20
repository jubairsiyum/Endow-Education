<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionPendingApprovalNotification extends Notification
{
    use Queueable;

    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $amount = (float) $this->transaction->amount;
        
        return [
            'type' => 'transaction_pending_approval',
            'title' => 'Transaction Pending Approval',
            'message' => 'New transaction awaiting approval: ' . $this->transaction->headline . ' (' . number_format($amount, 2) . ' ' . $this->transaction->currency . ')',
            'transaction_id' => $this->transaction->id,
            'headline' => $this->transaction->headline,
            'amount' => $this->transaction->amount,
            'currency' => $this->transaction->currency,
            'transaction_type' => $this->transaction->type,
            'created_by' => $this->transaction->creator->name,
            'url' => route('office.accounting.transactions.pending'),
            'icon' => 'fas fa-dollar-sign',
            'color' => 'warning',
        ];
    }
}
