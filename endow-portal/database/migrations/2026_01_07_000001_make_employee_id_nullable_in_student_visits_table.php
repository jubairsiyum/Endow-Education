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
            // Drop the existing foreign key constraint
            $table->dropForeign(['employee_id']);

            // Make employee_id nullable
            $table->foreignId('employee_id')->nullable()->change();

            // Re-add the foreign key with onDelete('set null')
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_visits', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['employee_id']);

            // Make employee_id non-nullable
            $table->foreignId('employee_id')->nullable(false)->change();

            // Re-add the foreign key with onDelete('cascade')
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
