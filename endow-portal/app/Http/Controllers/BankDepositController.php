<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankDepositController extends Controller
{
    /**
     * Display a listing of bank deposits.
     */
    public function index(Request $request)
    {
        $query = BankDeposit::with(['depositor', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('deposit_date', [$request->start_date, $request->end_date]);
        }

        // Filter by bank
        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'LIKE', '%' . $request->bank_name . '%');
        }

        $deposits = $query->orderBy('deposit_date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);

        // Get total deposited amount (approved only)
        $totalDeposited = BankDeposit::approved()->sum('amount');

        return view('accounting.bank-deposits.index', compact('deposits', 'totalDeposited'));
    }

    /**
     * Show the form for creating a new bank deposit.
     */
    public function create()
    {
        return view('accounting.bank-deposits.create');
    }

    /**
     * Store a newly created bank deposit in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:BDT,USD,KRW',
            'deposit_date' => 'required|date',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $validated['deposited_by'] = Auth::id();
        $validated['status'] = 'pending';

        $deposit = BankDeposit::create($validated);

        return redirect()->route('bank-deposits.show', $deposit)
                        ->with('success', 'Bank deposit recorded successfully and pending approval.');
    }

    /**
     * Display the specified bank deposit.
     */
    public function show(BankDeposit $bankDeposit)
    {
        $bankDeposit->load(['depositor', 'approver']);
        return view('accounting.bank-deposits.show', compact('bankDeposit'));
    }

    /**
     * Show the form for editing the specified bank deposit.
     */
    public function edit(BankDeposit $bankDeposit)
    {
        // Only allow editing if pending
        if (!$bankDeposit->isPending()) {
            return redirect()->route('bank-deposits.show', $bankDeposit)
                           ->with('error', 'Only pending deposits can be edited.');
        }

        return view('accounting.bank-deposits.edit', compact('bankDeposit'));
    }

    /**
     * Update the specified bank deposit in storage.
     */
    public function update(Request $request, BankDeposit $bankDeposit)
    {
        // Only allow updating if pending
        if (!$bankDeposit->isPending()) {
            return redirect()->route('bank-deposits.show', $bankDeposit)
                           ->with('error', 'Only pending deposits can be updated.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:BDT,USD,KRW',
            'deposit_date' => 'required|date',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $bankDeposit->update($validated);

        return redirect()->route('bank-deposits.show', $bankDeposit)
                        ->with('success', 'Bank deposit updated successfully.');
    }

    /**
     * Approve a bank deposit.
     */
    public function approve(BankDeposit $bankDeposit)
    {
        if (!$bankDeposit->isPending()) {
            return redirect()->route('bank-deposits.show', $bankDeposit)
                           ->with('error', 'Only pending deposits can be approved.');
        }

        $bankDeposit->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('bank-deposits.show', $bankDeposit)
                        ->with('success', 'Bank deposit approved successfully.');
    }

    /**
     * Reject a bank deposit.
     */
    public function reject(Request $request, BankDeposit $bankDeposit)
    {
        if (!$bankDeposit->isPending()) {
            return redirect()->route('bank-deposits.show', $bankDeposit)
                           ->with('error', 'Only pending deposits can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $bankDeposit->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('bank-deposits.show', $bankDeposit)
                        ->with('success', 'Bank deposit rejected.');
    }

    /**
     * Remove the specified bank deposit from storage.
     */
    public function destroy(BankDeposit $bankDeposit)
    {
        // Only allow deletion if pending
        if (!$bankDeposit->isPending()) {
            return redirect()->route('bank-deposits.index')
                           ->with('error', 'Only pending deposits can be deleted.');
        }

        $bankDeposit->delete();

        return redirect()->route('bank-deposits.index')
                        ->with('success', 'Bank deposit deleted successfully.');
    }

    /**
     * Display pending bank deposits for approval.
     */
    public function pending()
    {
        $deposits = BankDeposit::with(['depositor'])
                              ->pending()
                              ->orderBy('deposit_date', 'desc')
                              ->paginate(20);

        return view('accounting.bank-deposits.pending', compact('deposits'));
    }
}
