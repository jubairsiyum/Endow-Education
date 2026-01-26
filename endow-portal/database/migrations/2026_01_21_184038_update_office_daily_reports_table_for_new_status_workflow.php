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
        Schema::table('office_daily_reports', function (Blueprint $table) {
            // First, change status column to VARCHAR to allow all values during transition
            DB::statement("ALTER TABLE office_daily_reports MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");
        });

        // Convert existing status values
        DB::statement("UPDATE office_daily_reports SET status = 'in_progress' WHERE status = 'pending'");
        DB::statement("UPDATE office_daily_reports SET status = 'completed' WHERE status = 'reviewed'");

        Schema::table('office_daily_reports', function (Blueprint $table) {
            // Drop old department enum and add department_id foreign key
            $table->dropColumn('department');

            $table->foreignId('department_id')
                ->after('id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('set null');

            // Now update status to new enum
            DB::statement("ALTER TABLE office_daily_reports MODIFY COLUMN status ENUM('in_progress', 'review', 'completed') DEFAULT 'in_progress'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_daily_reports', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');

            $table->enum('department', [
                'marketing',
                'it',
                'consultant',
                'hr',
                'logistics'
            ])->after('id');

            // Revert status enum
            DB::statement("ALTER TABLE office_daily_reports MODIFY COLUMN status ENUM('pending', 'reviewed') DEFAULT 'pending'");
        });
    }
};
