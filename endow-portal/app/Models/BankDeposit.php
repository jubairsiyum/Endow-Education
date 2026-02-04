<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDeposit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'currency',
        'deposit_date',
        'bank_name',
        'account_number',
        'reference_number',
        'remarks',
        'deposited_by',
        'approved_by',
        'approved_at',
        'status',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'deposit_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who deposited the amount.
     */
    public function depositor()
    {
        return $this->belongsTo(User::class, 'deposited_by');
    }

    /**
     * Get the user who approved the deposit.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include pending deposits.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved deposits.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected deposits.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if deposit is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if deposit is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = $this->getCurrencySymbol();
        return $symbol . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get currency symbol.
     */
    public function getCurrencySymbol(): string
    {
        return match($this->currency) {
            'BDT' => '৳',
            'USD' => '$',
            'KRW' => '₩',
            default => $this->currency,
        };
    }
}
