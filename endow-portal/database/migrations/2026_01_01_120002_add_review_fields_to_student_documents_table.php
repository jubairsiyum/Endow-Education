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
        Schema::table('student_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('student_documents', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('student_documents', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            if (Schema::hasColumn('student_documents', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('student_documents', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
        });
    }
};
