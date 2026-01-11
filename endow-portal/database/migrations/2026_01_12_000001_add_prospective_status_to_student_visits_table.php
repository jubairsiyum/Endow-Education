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
        Schema::table('student_visits', function (Blueprint $table) {
            $table->enum('prospective_status', [
                'Prospective',
                'Under Review',
                'Eligibility Confirmed',
                'Needs Counseling',
                'Not Eligible'
            ])->default('Prospective')->after('email');

            // Add index for better query performance
            $table->index('prospective_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_visits', function (Blueprint $table) {
            $table->dropIndex(['prospective_status']);
            $table->dropColumn('prospective_status');
        });
    }
};
