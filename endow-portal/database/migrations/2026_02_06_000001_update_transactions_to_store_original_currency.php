<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates existing transactions to store amounts in their original currency
     * instead of converting everything to BDT.
     */
    public function up(): void
    {
        // Update transactions that have original_amount set
        // These were stored in BDT after conversion, but we need to restore the original amount
        DB::statement("
            UPDATE transactions 
            SET amount = COALESCE(original_amount, amount),
                original_amount = NULL,
                conversion_rate = NULL
            WHERE currency != 'BDT' AND original_amount IS NOT NULL
        ");
        
        // For transactions where currency is not BDT but original_amount is NULL,
        // the amount field already contains the correct value (no conversion was done)
        // So we just clear the conversion_rate
        DB::statement("
            UPDATE transactions 
            SET conversion_rate = NULL
            WHERE currency != 'BDT' AND original_amount IS NULL
        ");
        
        // Update bank deposits similarly if they have conversion logic
        if (Schema::hasTable('bank_deposits')) {
            // Check if bank_deposits has original_amount and conversion_rate columns
            if (Schema::hasColumn('bank_deposits', 'original_amount') && 
                Schema::hasColumn('bank_deposits', 'conversion_rate')) {
                DB::statement("
                    UPDATE bank_deposits 
                    SET amount = COALESCE(original_amount, amount),
                        original_amount = NULL,
                        conversion_rate = NULL
                    WHERE currency != 'BDT' AND original_amount IS NOT NULL
                ");
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This reverse migration cannot perfectly restore converted BDT values
     * because conversion rates may have changed. This is a data loss operation.
     */
    public function down(): void
    {
        // Cannot accurately reverse this migration as we've lost the conversion rates
        // and the converted BDT amounts. This is intentional as we want to move away
        // from the conversion model.
        
        // We'll just log a warning
        \Log::warning('Cannot reverse migration 2026_02_06_000001_update_transactions_to_store_original_currency - original conversion data has been removed');
    }
};
