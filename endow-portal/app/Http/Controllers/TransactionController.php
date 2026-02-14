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

        // Filter by currency
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
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
            // Store amount in its original currency without conversion
            $currency = $request->input('currency', 'BDT');
            $amount = $request->amount;

            $transaction = Transaction::create([
                'category_id' => $request->category_id,
                'headline' => $request->headline,
                'employee_id' => $request->employee_id,
                'amount' => $amount, // Store in original currency
                'currency' => $currency,
                'original_amount' => null, // No longer needed
                'conversion_rate' => null, // No longer needed
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
            // Store amount in its original currency without conversion
            $currency = $request->input('currency', 'BDT');
            $amount = $request->amount;

            $transaction->update([
                'category_id' => $request->category_id,
                'headline' => $request->headline,
                'employee_id' => $request->employee_id,
                'amount' => $amount, // Store in original currency
                'currency' => $currency,
                'original_amount' => null, // No longer needed
                'conversion_rate' => null, // No longer needed
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

            // Handle period shortcuts
            if ($request->filled('period')) {
                switch ($request->period) {
                    case 'today':
                        $startDate = now()->format('Y-m-d');
                        $endDate = now()->format('Y-m-d');
                        break;
                    case 'week':
                        $startDate = now()->startOfWeek()->format('Y-m-d');
                        $endDate = now()->endOfWeek()->format('Y-m-d');
                        break;
                    case 'month':
                        $startDate = now()->startOfMonth()->format('Y-m-d');
                        $endDate = now()->endOfMonth()->format('Y-m-d');
                        break;
                    case 'quarter':
                        $startDate = now()->startOfQuarter()->format('Y-m-d');
                        $endDate = now()->endOfQuarter()->format('Y-m-d');
                        break;
                    case 'year':
                        $startDate = now()->startOfYear()->format('Y-m-d');
                        $endDate = now()->endOfYear()->format('Y-m-d');
                        break;
                }
            }

            // Get approved transactions only
            $query = Transaction::approved()->financial()->dateRange($startDate, $endDate);

            // Filter by currency if specified
            $selectedCurrency = $request->filled('currency') ? $request->currency : null;
            if ($selectedCurrency) {
                $query->where('currency', $selectedCurrency);
            }

            // Always use 'amount' field since we store in original currency now
            $amountField = 'amount';
            
            // Get currency symbol
            $currencySymbol = match($selectedCurrency) {
                'USD' => '$',
                'KRW' => '₩',
                'BDT' => '৳',
                default => '৳' // Default to BDT when no filter
            };

            // Calculate totals using actual amounts in their currencies
            $totalIncome = (clone $query)->income()->sum($amountField) ?? 0;
            $totalExpense = (clone $query)->expense()->sum($amountField) ?? 0;
            $netProfit = $totalIncome - $totalExpense;

            // Get transaction counts
            $totalIncomeCount = (clone $query)->income()->count();
            $totalExpenseCount = (clone $query)->expense()->count();

            // Calculate Cash on Hand - CUMULATIVE up to end date (not just period range)
            // Cash on Hand is a balance, not a period metric
            $cashQuery = Transaction::approved()->financial();
            if ($selectedCurrency) {
                $cashQuery->where('currency', $selectedCurrency);
            }
            
            // All cash transactions UP TO the end date
            $cashIncome = (clone $cashQuery)
                ->income()
                ->where('payment_method', 'cash')
                ->where('entry_date', '<=', $endDate)
                ->sum($amountField) ?? 0;
                
            $cashExpense = (clone $cashQuery)
                ->expense()
                ->where('payment_method', 'cash')
                ->where('entry_date', '<=', $endDate)
                ->sum($amountField) ?? 0;

            // Get total deposited to bank - CUMULATIVE up to end date
            // Bank deposits reduce cash on hand, should be cumulative balance
            $totalDepositedToBank = 0;
            if (Schema::hasTable('bank_deposits')) {
                try {
                    $bankDepositsQuery = BankDeposit::approved()
                        ->where('deposit_date', '<=', $endDate);
                    
                    // Filter by currency if specified
                    if ($selectedCurrency) {
                        $bankDepositsQuery->where('currency', $selectedCurrency);
                    }
                    
                    $totalDepositedToBank = $bankDepositsQuery->sum('amount') ?? 0;
                } catch (\Exception $e) {
                    \Log::warning('Bank deposits query failed: ' . $e->getMessage());
                    $totalDepositedToBank = 0;
                }
            }
            
            // Cash on Hand = Total Cash Income - Total Cash Expense - Total Bank Deposits
            // This is a cumulative balance, not period-specific
            $totalCash = $cashIncome - $cashExpense - $totalDepositedToBank;


            // Get income by category with optimized query
            $incomeByCategory = Transaction::approved()
                ->financial()
                ->income()
                ->dateRange($startDate, $endDate)
                ->when($selectedCurrency, fn($q) => $q->where('currency', $selectedCurrency))
                ->select('category_id', DB::raw("SUM($amountField) as total"))
                ->groupBy('category_id')
                ->with('category:id,name,type')
                ->get();

            // Get expense by category with optimized query
            $expenseByCategory = Transaction::approved()
                ->financial()
                ->expense()
                ->dateRange($startDate, $endDate)
                ->when($selectedCurrency, fn($q) => $q->where('currency', $selectedCurrency))
                ->select('category_id', DB::raw("SUM($amountField) as total"))
                ->groupBy('category_id')
                ->with('category:id,name,type')
                ->get();

            // Get available currencies
            $currencies = Transaction::approved()
                ->select('currency')
                ->distinct()
                ->orderBy('currency')
                ->pluck('currency');

            // Prepare currency-specific summaries (using real amounts in each currency)
            $currencySummaries = [];
            foreach ($currencies as $curr) {
                $currQuery = Transaction::approved()->financial()->dateRange($startDate, $endDate)->where('currency', $curr);
                
                // Always use 'amount' since we now store in original currency
                $income = (clone $currQuery)->income()->sum('amount') ?? 0;
                $expense = (clone $currQuery)->expense()->sum('amount') ?? 0;
                $incomeCount = (clone $currQuery)->income()->count();
                $expenseCount = (clone $currQuery)->expense()->count();
                
                $currencySummaries[$curr] = [
                    'income' => $income,
                    'expense' => $expense,
                    'profit' => $income - $expense,
                    'income_count' => $incomeCount,
                    'expense_count' => $expenseCount,
                    'total_transactions' => $incomeCount + $expenseCount,
                ];
            }

            // Get recent transactions with selective column loading
            $recentTransactions = Transaction::approved()
                ->financial()
                ->select('id', 'entry_date', 'type', 'amount', 'currency', 'student_name', 'headline', 'payment_method', 'category_id', 'created_by')
                ->with([
                    'category:id,name',
                    'creator:id,name'
                ])
                ->when($selectedCurrency, fn($q) => $q->where('currency', $selectedCurrency))
                ->dateRange($startDate, $endDate)
                ->orderBy('entry_date', 'desc')
                ->limit(10)
                ->get();

            // Try to use enhanced view, fallback to regular view if not found
            $viewName = view()->exists('accounting.summary-enhanced') 
                ? 'accounting.summary-enhanced' 
                : 'accounting.summary';
            
            return view($viewName, compact(
                'totalIncome',
                'totalExpense',
                'netProfit',
                'totalCash',
                'totalDepositedToBank',
                'cashIncome',
                'cashExpense',
                'totalIncomeCount',
                'totalExpenseCount',
                'incomeByCategory',
                'expenseByCategory',
                'recentTransactions',
                'startDate',
                'endDate',
                'currencies',
                'selectedCurrency',
                'currencySummaries',
                'currencySymbol'
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

    /**
     * Export transactions to Excel/CSV format.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->filled('start_date') 
                ? $request->start_date 
                : now()->startOfMonth()->format('Y-m-d');
            
            $endDate = $request->filled('end_date') 
                ? $request->end_date 
                : now()->endOfMonth()->format('Y-m-d');

            // Filter by currency if specified
            $currency = $request->filled('currency') ? $request->currency : null;
            
            // Get all approved transactions for the period
            $query = Transaction::approved()
                ->dateRange($startDate, $endDate)
                ->with(['category', 'creator', 'employee'])
                ->orderBy('entry_date', 'desc');
            
            // Apply currency filter if specified
            if ($currency) {
                $query->where('currency', $currency);
            }
            
            // Option to include or exclude non-financial entries
            $includeNonFinancial = $request->filled('include_non_financial') ? $request->boolean('include_non_financial') : false;
            if (!$includeNonFinancial) {
                $query->financial();
            }
            
            $transactions = $query->get();

            // Calculate summary (only for financial transactions in selected currency)
            $financialTransactions = $transactions->where('type', '!=', 'non_financial');
            $totalIncome = $financialTransactions->where('type', 'income')->sum('amount');
            $totalExpense = $financialTransactions->where('type', 'expense')->sum('amount');
            $netProfit = $totalIncome - $totalExpense;

            // Set headers for CSV download
            $currencyLabel = $currency ? "_{$currency}" : "_all_currencies";
            $filename = "accounting_report{$currencyLabel}_{$startDate}_to_{$endDate}.csv";
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write summary header
            fputcsv($output, ['ENDOW CORPORATION - ACCOUNTING REPORT']);
            fputcsv($output, ['Period:', $startDate . ' to ' . $endDate]);
            if ($currency) {
                fputcsv($output, ['Currency Filter:', $currency]);
            }
            fputcsv($output, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($output, []);
            
            // Write summary
            $currencyLabel = $currency ? $currency : 'Mixed Currencies';
            fputcsv($output, ['SUMMARY (FINANCIAL TRANSACTIONS ONLY)']);
            fputcsv($output, ['Total Income (' . $currencyLabel . ')', number_format($totalIncome, 2)]);
            fputcsv($output, ['Total Expense (' . $currencyLabel . ')', number_format($totalExpense, 2)]);
            fputcsv($output, ['Net Profit/Loss (' . $currencyLabel . ')', number_format($netProfit, 2)]);
            fputcsv($output, []);
            
            // Write transaction headers
            fputcsv($output, [
                'Date',
                'Type',
                'Category',
                'Headline',
                'Student Name',
                'Amount',
                'Currency',
                'Payment Method',
                'Employee',
                'Remarks',
                'Created By',
                'Status',
                'Approved At'
            ]);
            
            // Write transaction data
            foreach ($transactions as $transaction) {
                fputcsv($output, [
                    $transaction->entry_date && is_object($transaction->entry_date) ? $transaction->entry_date->format('Y-m-d') : ($transaction->entry_date ?? '-'),
                    ucfirst($transaction->type),
                    $transaction->category->name ?? 'N/A',
                    $transaction->headline ?? '-',
                    $transaction->student_name ?? '-',
                    number_format((float)$transaction->amount, 2),
                    $transaction->currency ?? 'BDT',
                    ucfirst($transaction->payment_method ?? 'N/A'),
                    $transaction->employee->name ?? '-',
                    $transaction->remarks ?? '-',
                    $transaction->creator->name ?? 'N/A',
                    ucfirst($transaction->status),
                    $transaction->approved_at ? $transaction->approved_at->format('Y-m-d H:i:s') : '-'
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (\Exception $e) {
            \Log::error('Transaction Export Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export transactions: ' . $e->getMessage());
        }
    }
}
