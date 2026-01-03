<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'bKash', 'Rocket', 'Nagad', 'Other'])->default('Cash');
            $table->enum('payment_type', ['Tuition Fee', 'Service Fee', 'Processing Fee', 'Consultation Fee', 'Document Fee', 'Other'])->default('Service Fee');
            $table->date('payment_date');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending', 'Confirmed', 'Cancelled'])->default('Confirmed');
            $table->foreignId('received_by')->constrained('users')->onDelete('restrict'); // Employee who recorded the payment
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('student_id');
            $table->index('payment_date');
            $table->index('received_by');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
