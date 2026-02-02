<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-transaction');
    }

    /**
     * Determine whether the user can view the transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('view-transaction');
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-transaction');
    }

    /**
     * Determine whether the user can update the transaction.
     * Only pending transactions can be updated.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Must have update permission
        if (!$user->hasPermissionTo('update-transaction')) {
            return false;
        }

        // Only pending transactions can be updated
        if (!$transaction->isPending()) {
            return false;
        }

        // Users can only update their own pending transactions
        // unless they are Super Admin or have special permission
        if ($user->hasRole('Super Admin') || $user->hasRole('Accountant')) {
            return true;
        }

        return $transaction->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the transaction.
     * Approved transactions cannot be deleted.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Must have delete permission
        if (!$user->hasPermissionTo('delete-transaction')) {
            return false;
        }

        // Approved transactions cannot be deleted
        if ($transaction->isApproved()) {
            return false;
        }

        // Super Admin and Accountant can delete any non-approved transaction
        if ($user->hasRole('Super Admin') || $user->hasRole('Accountant')) {
            return true;
        }

        // Users can only delete their own pending/rejected transactions
        return $transaction->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the transaction.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the transaction.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can approve transactions.
     */
    public function approve(User $user, Transaction $transaction): bool
    {
        // Must have approve permission
        if (!$user->hasPermissionTo('approve-transaction')) {
            return false;
        }

        // Only pending transactions can be approved
        if (!$transaction->isPending()) {
            return false;
        }

        // Users cannot approve their own transactions
        if ($transaction->created_by === $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can reject transactions.
     */
    public function reject(User $user, Transaction $transaction): bool
    {
        // Same logic as approve
        return $this->approve($user, $transaction);
    }

    /**
     * Determine whether the user can view accounting summary.
     */
    public function viewSummary(User $user): bool
    {
        return $user->hasPermissionTo('view-accounting-summary');
    }

    /**
     * Determine whether the user can export transaction data.
     */
    public function export(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Accountant');
    }
}
