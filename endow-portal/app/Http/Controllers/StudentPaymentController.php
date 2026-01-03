<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return view('students.payments.create', compact('student'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,bKash,Rocket,Nagad,Other',
            'payment_type' => 'required|in:Tuition Fee,Service Fee,Processing Fee,Consultation Fee,Document Fee,Other',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        DB::beginTransaction();

        try {
            $payment = StudentPayment::create([
                'student_id' => $student->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_type' => $validated['payment_type'],
                'payment_date' => $validated['payment_date'],
                'transaction_id' => $validated['transaction_id'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
                'received_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('students.payments.index', $student)
                ->with('success', 'Payment recorded successfully!');

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

        return view('students.payments.edit', compact('student', 'payment'));
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

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,bKash,Rocket,Nagad,Other',
            'payment_type' => 'required|in:Tuition Fee,Service Fee,Processing Fee,Consultation Fee,Document Fee,Other',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        DB::beginTransaction();

        try {
            $payment->update($validated);

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
