<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\AccountCategory;
use App\Models\BankDeposit;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        // Eager load relationships to prevent N+1 queries
        $query = Transaction::with(['category', 'creator', 'approver', 'employee']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Order by entry date descending
        $transactions = $query->orderBy('entry_date', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);

        // Cache active categories for 1 hour since they rarely change
        $categories = Cache::remember('active_account_categories', 3600, function () {
            return AccountCategory::active()->orderBy('type')->orderBy('name')->get();
        });

        return view('accounting.transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        // Cache active categories for 1 hour
        $categories = Cache::remember('active_account_categories', 3600, function () {
            return AccountCategory::active()->orderBy('type')->orderBy('name')->get();
        });
        
        // Cache employees list for 30 minutes (exclude students)
        $employees = Cache::remember('office_employees_list', 1800, function () {
            return \App\Models\User::select('id', 'name', 'email')
                ->role(['Admin', 'Super Admin', 'Employee']) // Exclude Student role
                ->orderBy('name')
                ->get();
        });
            
        return view('accounting.transactions.create', compact('categories', 'employees'));
    }

    /**
     * Search students for autocomplete (AJAX endpoint).
     */
    public function searchStudents(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $students = \App\Models\Student::query()
            ->with(['targetUniversity', 'targetProgram'])
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('registration_id', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'text' => $student->name,
                    'email' => $student->email,
                    'university' => $student->targetUniversity ? $student->targetUniversity->name : 'N/A',
                    'program' => $student->targetProgram ? $student->targetProgram->name : 'N/A',
                    'registration_id' => $student->registration_id ?? 'N/A',
                ];
            });

        return response()->json($students);
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(TransactionRequest $request)
    {
        DB::beginTransaction();

        try {
            // Get conversion rate if not BDT
            $currency = $request->input('currency', 'BDT');
            $originalAmount = $request->amount;
            $conversionRate = null;
            $amountInBDT = $originalAmount;

            if ($currency !== 'BDT') {
                // Get today's conversion rate
                $conversionRate = $this->getConversionRate($currency);
                $amountInBDT = $originalAmount * $conversionRate;
            }

            $transaction = Transaction::create([
                'category_id' => $request->category_id,
                'headline' => $request->headline,
                'employee_id' => $request->employee_id,
                'amount' => $amountInBDT, // Store in BDT
                'currency' => $currency,
                'original_amount' => $currency !== 'BDT' ? $originalAmount : null,
                'conversion_rate' => $conversionRate,
                'entry_date' => $request->entry_date,
                'remarks' => $request->remarks,
                'student_name' => $request->student_name,
                'payment_method' => $request->payment_method,
                'type' => $request->type,
                'status' => 'pending', // Always start as pending
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            // Clear pending transactions count cache
            Cache::forget('pending_transactions_count');

            return redirect()
                ->route('office.accounting.transactions.index')
                ->with('success', 'Transaction created successfully and pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Get conversion rate for currency to BDT.
     */
    private function getConversionRate(string $currency): float
    {
        // Conversion rates to BDT (these should be updated daily in production)
        // In production, you might want to fetch this from an API or database
        $rates = [
            'USD' => 110.50, // 1 USD = 110.50 BDT
            'KRW' => 0.092,  // 1 KRW = 0.092 BDT
        ];

        return $rates[$currency] ?? 1;
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        // Eager load all relationships at once
        $transaction->load(['category', 'creator', 'approver', 'employee', 'studentPayment']);
        return view('accounting.transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        // Only pending transactions can be edited
        if (!$transaction->isPending()) {
            return redirect()
                ->route('office.accounting.transactions.index')
                ->with('error', 'Only pending transactions can be edited.');
        }

        // Use cached categories
        $categories = Cache::remember('active_account_categories', 3600, function () {
            return AccountCategory::active()->orderBy('type')->orderBy('name')->get();
        });
        
        // Use cached employees list
        $employees = Cache::remember('office_employees_list', 1800, function () {
            return \App\Models\User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
        });
            
        return view('accounting.transactions.edit', compact('transaction', 'categories', 'employees'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(TransactionRequest $request, Transaction $transaction)
    {
        // Only pending transactions can be updated
        if (!$transaction->isPending()) {
            return redirect()
                ->route('office.accounting.transactions.index')
                ->with('error', 'Only pending transactions can be updated.');
        }

        DB::beginTransaction();

        try {
            // Get conversion rate if not BDT
            $currency = $request->input('currency', 'BDT');
            $originalAmount = $request->amount;
            $conversionRate = null;
            $amountInBDT = $originalAmount;

            if ($currency !== 'BDT') {
                // Get today's conversion rate
                $conversionRate = $this->getConversionRate($currency);
                $amountInBDT = $originalAmount * $conversionRate;
            }

            $transaction->update([
                'category_id' => $request->category_id,
                'headline' => $request->headline,
                'employee_id' => $request->employee_id,
                'amount' => $amountInBDT, // Store in BDT
                'currency' => $currency,
                'original_amount' => $currency !== 'BDT' ? $originalAmount : null,
                'conversion_rate' => $conversionRate,
                'entry_date' => $request->entry_date,
                'remarks' => $request->remarks,
                'student_name' => $request->student_name,
                'payment_method' => $request->payment_method,
                'type' => $request->type,
            ]);

            DB::commit();

            // Clear pending transactions count cache
            Cache::forget('pending_transactions_count');

            return redirect()
                ->route('office.accounting.transactions.index')
                ->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update transaction: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Only pending or rejected transactions can be deleted
        if ($transaction->isApproved()) {
            return redirect()
                ->route('accounting.transactions.index')
                ->with('error', 'Approved transactions cannot be deleted.');
        }

        try {
            $transaction->delete();
            
            // Clear pending transactions count cache
            Cache::forget('pending_transactions_count');
            
            return redirect()
                ->route('accounting.transactions.index')
                ->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    /**
     * Show pending transactions for approval.
     */
    public function pending()
    {
        // Eager load all necessary relationships
        $transactions = Transaction::with(['category', 'creator', 'employee'])
            ->pending()
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('accounting.transactions.pending', compact('transactions'));
    }

    /**
     * Approve a transaction.
     */
    public function approve(Transaction $transaction)
    {
        if (!$transaction->isPending()) {
            return redirect()
                ->route('office.accounting.transactions.pending')
                ->with('error', 'Only pending transactions can be approved.');
        }

        DB::beginTransaction();

        try {
            $transaction->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            // Clear pending transactions count cache
            Cache::forget('pending_transactions_count');

            return redirect()
                ->route('office.accounting.transactions.pending')
                ->with('success', 'Transaction approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve transaction: ' . $e->getMessage());
        }
    }

    /**
     * Reject a transaction.
     */
    public function reject(Request $request, Transaction $transaction)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        if (!$transaction->isPending()) {
            return redirect()
                ->route('office.accounting.transactions.pending')
                ->with('error', 'Only pending transactions can be rejected.');
        }

        DB::beginTransaction();

        try {
            $transaction->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            DB::commit();

            // Clear pending transactions count cache
            Cache::forget('pending_transactions_count');

            return redirect()
                ->route('office.accounting.transactions.pending')
                ->with('success', 'Transaction rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject transaction: ' . $e->getMessage());
        }
    }

    /**
     * Display accounting summary.
     */
    public function summary(Request $request)
    {
        try {
            // Check if transactions table exists
            if (!Schema::hasTable('transactions')) {
                return back()->with('error', 'Accounting tables not found. Please run migrations: php artisan migrate');
            }

            // Default to current month
            $startDate = $request->filled('start_date') 
                ? $request->start_date 
                : now()->startOfMonth()->format('Y-m-d');
            
            $endDate = $request->filled('end_date') 
                ? $request->end_date 
                : now()->endOfMonth()->format('Y-m-d');

            // Get approved transactions only
            $query = Transaction::approved()->dateRange($startDate, $endDate);

            // Calculate totals
            $totalIncome = (clone $query)->income()->sum('amount') ?? 0;
            $totalExpense = (clone $query)->expense()->sum('amount') ?? 0;
            $netProfit = $totalIncome - $totalExpense;

            // Calculate Cash Income and Cash Expense (payment_method = 'cash')
            $cashIncome = (clone $query)->income()->where('payment_method', 'cash')->sum('amount') ?? 0;
            $cashExpense = (clone $query)->expense()->where('payment_method', 'cash')->sum('amount') ?? 0;

            // Get total deposited to bank (this reduces cash on hand)
            // Check if bank_deposits table exists before querying
            $totalDepositedToBank = 0;
            if (Schema::hasTable('bank_deposits')) {
                try {
                    $totalDepositedToBank = BankDeposit::approved()
                        ->whereBetween('deposit_date', [$startDate, $endDate])
                        ->sum('amount') ?? 0;
                } catch (\Exception $e) {
                    \Log::warning('Bank deposits query failed: ' . $e->getMessage());
                    $totalDepositedToBank = 0;
                }
            }
            
            // Cash on Hand = Cash Income - Cash Expense - Bank Deposits
            // Bank deposits come FROM cash, so they reduce the cash on hand
            $totalCash = $cashIncome - $cashExpense - $totalDepositedToBank;

            // Get income by category with optimized query
            $incomeByCategory = Transaction::approved()
                ->income()
                ->dateRange($startDate, $endDate)
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->with('category:id,name,type')
                ->get();

            // Get expense by category with optimized query
            $expenseByCategory = Transaction::approved()
                ->expense()
                ->dateRange($startDate, $endDate)
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->with('category:id,name,type')
                ->get();

            // Get recent transactions with selective column loading
            $recentTransactions = Transaction::approved()
                ->select('id', 'entry_date', 'type', 'amount', 'student_name', 'category_id', 'created_by')
                ->with([
                    'category:id,name',
                    'creator:id,name'
                ])
                ->dateRange($startDate, $endDate)
                ->orderBy('entry_date', 'desc')
                ->limit(10)
                ->get();

            return view('accounting.summary', compact(
                'totalIncome',
                'totalExpense',
                'netProfit',
                'totalCash',
                'totalDepositedToBank',
                'incomeByCategory',
                'expenseByCategory',
                'recentTransactions',
                'startDate',
                'endDate'
            ));
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Accounting Summary Error: ' . $e->getMessage());
            
            // Check for specific error codes
            if (str_contains($e->getMessage(), "Table") && str_contains($e->getMessage(), "doesn't exist")) {
                return back()->with('error', 'Accounting tables missing. Run: php artisan migrate');
            }
            
            return back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Accounting Summary Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading summary: ' . $e->getMessage());
        }
    }
}
