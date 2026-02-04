<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Calculate current available cash on hand
        $availableCash = $this->calculateAvailableCash(date('Y-m-d'));
        
        return view('accounting.bank-deposits.create', compact('availableCash'));
    }

    /**
     * Store a newly created bank deposit in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'required|string|in:BDT,USD,KRW',
                'deposit_date' => 'required|date',
                'bank_name' => 'required|string|max:255',
                'account_number' => 'nullable|string|max:255',
                'reference_number' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            // Calculate available cash on hand to validate deposit amount
            $availableCash = $this->calculateAvailableCash($validated['deposit_date']);
            
            // Check if deposit amount exceeds available cash
            if ($validated['amount'] > $availableCash) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', sprintf(
                        'Insufficient cash on hand. Available cash: %s %s. Cannot deposit %s %s.',
                        number_format($availableCash, 2),
                        $validated['currency'],
                        number_format($validated['amount'], 2),
                        $validated['currency']
                    ));
            }

            $validated['deposited_by'] = Auth::id();
            $validated['status'] = 'pending';

            $deposit = BankDeposit::create($validated);

            DB::commit();

            return redirect()->route('office.accounting.bank-deposits.show', $deposit)
                            ->with('success', sprintf(
                                'Bank deposit of %s %s recorded successfully and pending approval. Remaining cash on hand: %s %s',
                                number_format($validated['amount'], 2),
                                $validated['currency'],
                                number_format($availableCash - $validated['amount'], 2),
                                $validated['currency']
                            ));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bank deposit creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to create bank deposit: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified bank deposit.
     */
    public function show(BankDeposit $bankDeposit)
    {
        try {
            // Safely load relationships - handle missing relationships gracefully
            $relations = [];
            if (method_exists($bankDeposit, 'depositor')) {
                $relations[] = 'depositor';
            }
            if (method_exists($bankDeposit, 'approver')) {
                $relations[] = 'approver';
            }
            
            if (!empty($relations)) {
                $bankDeposit->load($relations);
            }
            
            return view('accounting.bank-deposits.show', compact('bankDeposit'));
        } catch (\Exception $e) {
            \Log::error('Bank deposit show failed: ' . $e->getMessage());
            return redirect()->route('office.accounting.bank-deposits.index')
                ->with('error', 'Unable to load bank deposit details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified bank deposit.
     */
    public function edit(BankDeposit $bankDeposit)
    {
        try {
            // Only allow editing if pending
            if (!$bankDeposit->isPending()) {
                return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
                               ->with('error', 'Only pending deposits can be edited.');
            }

            return view('accounting.bank-deposits.edit', compact('bankDeposit'));
        } catch (\Exception $e) {
            \Log::error('Bank deposit edit failed: ' . $e->getMessage());
            return redirect()->route('office.accounting.bank-deposits.index')
                ->with('error', 'Unable to edit bank deposit: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified bank deposit in storage.
     */
    public function update(Request $request, BankDeposit $bankDeposit)
    {
        try {
            // Only allow updating if pending
            if (!$bankDeposit->isPending()) {
                return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
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

            DB::beginTransaction();
            $bankDeposit->update($validated);
            DB::commit();

            return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
                            ->with('success', 'Bank deposit updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bank deposit update failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to update bank deposit: ' . $e->getMessage());
        }
    }

    /**
     * Approve a bank deposit.
     */
    public function approve(BankDeposit $bankDeposit)
    {
        try {
            if (!$bankDeposit->isPending()) {
                return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
                               ->with('error', 'Only pending deposits can be approved.');
            }

            DB::beginTransaction();

            $bankDeposit->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
                            ->with('success', 'Bank deposit approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank deposit approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve bank deposit: ' . $e->getMessage());
        }
    }

    /**
     * Reject a bank deposit.
     */
    public function reject(Request $request, BankDeposit $bankDeposit)
    {
        try {
            if (!$bankDeposit->isPending()) {
                return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
                               ->with('error', 'Only pending deposits can be rejected.');
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            $bankDeposit->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            DB::commit();

            return redirect()->route('office.accounting.bank-deposits.show', $bankDeposit)
                            ->with('success', 'Bank deposit rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank deposit rejection failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to reject bank deposit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bank deposit from storage.
     */
    public function destroy(BankDeposit $bankDeposit)
    {
        try {
            // Only allow deletion if pending
            if (!$bankDeposit->isPending()) {
                return redirect()->route('office.accounting.bank-deposits.index')
                               ->with('error', 'Only pending deposits can be deleted.');
            }

            DB::beginTransaction();
            $bankDeposit->delete();
            DB::commit();

            return redirect()->route('office.accounting.bank-deposits.index')
                            ->with('success', 'Bank deposit deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank deposit deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete bank deposit: ' . $e->getMessage());
        }
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

    /**
     * Calculate available cash on hand up to a given date.
     * Cash on hand = Cash Income - Cash Expense - Previous Bank Deposits (approved)
     * 
     * @param string $date The date to calculate cash up to (format: Y-m-d)
     * @return float Available cash amount
     */
    private function calculateAvailableCash($date)
    {
        try {
            // Get all approved cash transactions up to the given date
            $cashIncome = \App\Models\Transaction::approved()
                ->income()
                ->where('payment_method', 'cash')
                ->where('entry_date', '<=', $date)
                ->sum('amount') ?? 0;

            $cashExpense = \App\Models\Transaction::approved()
                ->expense()
                ->where('payment_method', 'cash')
                ->where('entry_date', '<=', $date)
                ->sum('amount') ?? 0;

            // Get all approved bank deposits up to the given date
            $totalDeposited = BankDeposit::approved()
                ->where('deposit_date', '<=', $date)
                ->sum('amount') ?? 0;

            // Available Cash = Cash Income - Cash Expense - Already Deposited
            $availableCash = $cashIncome - $cashExpense - $totalDeposited;

            return max($availableCash, 0); // Cannot be negative
        } catch (\Exception $e) {
            \Log::error('Calculate available cash failed: ' . $e->getMessage());
            return 0;
        }
    }
}
