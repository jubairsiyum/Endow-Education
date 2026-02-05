<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add 'non_financial' to the enum (keeping 'journal' temporarily)
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income', 'expense', 'journal', 'non_financial') NOT NULL");
        
        // Then, update existing 'journal' records to 'non_financial'
        DB::statement("UPDATE transactions SET type = 'non_financial' WHERE type = 'journal'");
        
        // Finally, remove 'journal' from the enum
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income', 'expense', 'non_financial') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'non_financial' back to 'journal'
        DB::statement("UPDATE transactions SET type = 'journal' WHERE type = 'non_financial'");
        
        // Revert the enum
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('income', 'expense', 'journal') NOT NULL");
    }
};
