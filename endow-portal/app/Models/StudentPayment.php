<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'amount',
        'payment_method',
        'payment_type',
        'payment_date',
        'transaction_id', // Mobile banking transaction ID
        'accounting_transaction_id', // Link to accounting transactions table
        'notes',
        'status',
        'received_by',
        'created_by',
    ];

    /**
     * Get the accounting transaction.
     */
    public function accountingTransaction()
    {
        return $this->belongsTo(Transaction::class, 'accounting_transaction_id');
    }

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the student that owns the payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the employee who received the payment.
     */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for confirmed payments.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'Confirmed');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return '৳ ' . number_format($this->amount, 2);
    }
}
