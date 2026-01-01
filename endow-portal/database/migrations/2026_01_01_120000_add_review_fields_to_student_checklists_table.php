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
        Schema::table('student_checklists', function (Blueprint $table) {
            if (!Schema::hasColumn('student_checklists', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('student_checklists', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('student_checklists', 'feedback')) {
                $table->text('feedback')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_checklists', function (Blueprint $table) {
            if (Schema::hasColumn('student_checklists', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('student_checklists', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('student_checklists', 'feedback')) {
                $table->dropColumn('feedback');
            }
        });
    }
};
