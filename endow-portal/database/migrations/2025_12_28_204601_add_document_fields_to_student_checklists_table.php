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
            $table->longText('document_data')->nullable()->after('document_path');
            $table->string('document_mime_type')->nullable()->after('document_data');
            $table->string('document_original_name')->nullable()->after('document_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_checklists', function (Blueprint $table) {
            $table->dropColumn(['document_data', 'document_mime_type', 'document_original_name']);
        });
    }
};
