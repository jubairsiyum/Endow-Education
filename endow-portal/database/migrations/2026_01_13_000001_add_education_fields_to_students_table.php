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
        Schema::table('students', function (Blueprint $table) {
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('students', 'ssc_year')) {
                $table->year('ssc_year')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('students', 'ssc_result')) {
                $table->string('ssc_result')->nullable()->after('ssc_year');
            }
            if (!Schema::hasColumn('students', 'hsc_year')) {
                $table->year('hsc_year')->nullable()->after('ssc_result');
            }
            if (!Schema::hasColumn('students', 'hsc_result')) {
                $table->string('hsc_result')->nullable()->after('hsc_year');
            }
            if (!Schema::hasColumn('students', 'has_ielts')) {
                $table->boolean('has_ielts')->default(false)->after('hsc_result');
            }
            if (!Schema::hasColumn('students', 'ielts_score')) {
                $table->decimal('ielts_score', 3, 1)->nullable()->after('has_ielts')->comment('IELTS score (e.g., 6.5, 7.0)');
            }

            // Add indexes for better query performance
            if (!Schema::hasIndex('students', ['ssc_year'])) {
                $table->index('ssc_year');
            }
            if (!Schema::hasIndex('students', ['hsc_year'])) {
                $table->index('hsc_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasIndex('students', ['ssc_year'])) {
                $table->dropIndex(['ssc_year']);
            }
            if (Schema::hasIndex('students', ['hsc_year'])) {
                $table->dropIndex(['hsc_year']);
            }

            $columns = ['ielts_score', 'has_ielts', 'hsc_result', 'hsc_year', 'ssc_result', 'ssc_year'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
