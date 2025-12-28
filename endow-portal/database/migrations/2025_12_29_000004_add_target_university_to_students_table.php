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
            $table->foreignId('target_university_id')->nullable()->after('course')->constrained('universities')->onDelete('set null');
            $table->foreignId('target_program_id')->nullable()->after('target_university_id')->constrained('programs')->onDelete('set null');

            // Indexes
            $table->index('target_university_id');
            $table->index('target_program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['target_university_id']);
            $table->dropForeign(['target_program_id']);
            $table->dropColumn(['target_university_id', 'target_program_id']);
        });
    }
};
