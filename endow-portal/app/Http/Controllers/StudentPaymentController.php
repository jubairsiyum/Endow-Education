<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\Transaction;
use App\Models\AccountCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StudentPaymentController extends Controller
{
    /**
     * Display a listing of payments for a student.
     */
    public function index(Student $student)
    {
        $this->authorize('view', $student);

        $payments = $student->payments()
            ->with(['receivedBy', 'createdBy'])
            ->latest('payment_date')
            ->paginate(15);

        $totalPaid = $student->payments()->confirmed()->sum('amount');

        return view('students.payments.index', compact('student', 'payments', 'totalPaid'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Student $student)
    {
        $this->authorize('update', $student);

        // Cache active income categories for payment types (1 hour)
        $paymentTypes = Cache::remember('income_payment_types', 3600, function () {
            return AccountCategory::active()
                ->where('type', 'income')
                ->orderBy('name')
                ->get();
        });

        return view('students.payments.create', compact('student', 'paymentTypes'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        // Get active income category IDs for validation (cached)
        $validCategoryIds = Cache::remember('valid_income_category_ids', 3600, function () {
            return AccountCategory::active()
                ->where('type', 'income')
                ->pluck('id')
                ->toArray();
        });

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,bKash,Rocket,Nagad,Other',
            'payment_type_id' => 'required|exists:account_categories,id|in:' . implode(',', $validCategoryIds),
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        DB::beginTransaction();

        try {
            // Get the selected category
            $accountCategory = AccountCategory::findOrFail($validated['payment_type_id']);

            // Create student payment record
            $payment = StudentPayment::create([
                'student_id' => $student->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_type' => $accountCategory->name, // Store category name
                'payment_date' => $validated['payment_date'],
                'transaction_id' => $validated['transaction_id'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
                'received_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            // Create corresponding accounting transaction (pending approval)
            $accountingTransaction = Transaction::create([
                'category_id' => $accountCategory->id,
                'amount' => $validated['amount'],
                'entry_date' => $validated['payment_date'],
                'remarks' => "Student payment from {$student->name} - {$accountCategory->name}" . 
                             ($validated['notes'] ? "\nNotes: {$validated['notes']}" : ''),
                'student_name' => $student->name,
                'payment_method' => $validated['payment_method'],
                'type' => 'income',
                'status' => 'pending', // Always pending for accountant approval
                'created_by' => Auth::id(),
            ]);

            // Link payment to accounting transaction
            $payment->update(['accounting_transaction_id' => $accountingTransaction->id]);

            DB::commit();

            // Clear pending transactions count cache
            Cache::forget('pending_transactions_count');

            return redirect()
                ->route('students.payments.index', $student)
                ->with('success', 'Payment recorded successfully and submitted for accounting approval!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Student $student, StudentPayment $payment)
    {
        $this->authorize('update', $student);

        if ($payment->student_id !== $student->id) {
            abort(404);
        }

        // Cache active income categories for payment types (1 hour)
        $paymentTypes = Cache::remember('income_payment_types', 3600, function () {
            return AccountCategory::active()
                ->where('type', 'income')
                ->orderBy('name')
                ->get();
        });

        return view('students.payments.edit', compact('student', 'payment', 'paymentTypes'));
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Student $student, StudentPayment $payment)
    {
        $this->authorize('update', $student);

        if ($payment->student_id !== $student->id) {
            abort(404);
        }

        // Get active income category IDs for validation (cached)
        $validCategoryIds = Cache::remember('valid_income_category_ids', 3600, function () {
            return AccountCategory::active()
                ->where('type', 'income')
                ->pluck('id')
                ->toArray();
        });

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,bKash,Rocket,Nagad,Other',
            'payment_type_id' => 'required|exists:account_categories,id|in:' . implode(',', $validCategoryIds),
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        DB::beginTransaction();

        try {
            // Get the selected category
            $accountCategory = AccountCategory::findOrFail($validated['payment_type_id']);

            $payment->update([
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_type' => $accountCategory->name, // Store category name
                'payment_date' => $validated['payment_date'],
                'transaction_id' => $validated['transaction_id'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()
                ->route('students.payments.index', $student)
                ->with('success', 'Payment updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update payment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Student $student, StudentPayment $payment)
    {
        $this->authorize('update', $student);

        if ($payment->student_id !== $student->id) {
            abort(404);
        }

        try {
            $payment->delete();

            return redirect()
                ->route('students.payments.index', $student)
                ->with('success', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }
}
