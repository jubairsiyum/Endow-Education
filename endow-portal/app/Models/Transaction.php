<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'headline',
        'amount',
        'currency',
        'original_amount',
        'conversion_rate',
        'entry_date',
        'remarks',
        'student_name',
        'payment_method',
        'type',
        'status',
        'created_by',
        'employee_id',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
        'entry_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the category that owns the transaction.
     */
    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'category_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the employee associated with the transaction.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the user who approved the transaction.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the student payment associated with this transaction.
     */
    public function studentPayment()
    {
        return $this->hasOne(StudentPayment::class, 'accounting_transaction_id');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved transactions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected transactions.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include income transactions.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expense transactions.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('entry_date', now()->year)
                     ->whereMonth('entry_date', now()->month);
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if transaction is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
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
