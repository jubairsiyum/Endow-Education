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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('headline')->nullable()->after('category_id');
            $table->foreignId('employee_id')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->string('currency', 10)->default('BDT')->after('amount');
            $table->decimal('original_amount', 15, 2)->nullable()->after('currency');
            $table->decimal('conversion_rate', 10, 4)->nullable()->after('original_amount');
            
            $table->index('employee_id');
            $table->index('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['headline', 'employee_id', 'currency', 'original_amount', 'conversion_rate']);
        });
    }
};
