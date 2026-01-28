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
        Schema::table('student_payments', function (Blueprint $table) {
            // Change payment_type from ENUM to VARCHAR to support dynamic account categories
            $table->string('payment_type', 255)->default('Service Fee')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            // Revert back to ENUM (for rollback)
            $table->enum('payment_type', ['Tuition Fee', 'Service Fee', 'Processing Fee', 'Consultation Fee', 'Document Fee', 'Other'])->default('Service Fee')->change();
        });
    }
};
