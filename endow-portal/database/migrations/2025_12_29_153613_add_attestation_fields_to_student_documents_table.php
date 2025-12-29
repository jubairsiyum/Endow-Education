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
            $table->string('document_type')->nullable()->after('student_checklist_id');
            $table->string('document_category')->nullable()->after('document_type'); // attestation or translation
            $table->integer('copy_number')->default(1)->after('document_category'); // which copy (1-5)
            $table->text('attestation_details')->nullable()->after('copy_number'); // JSON for attestation tracking
            $table->boolean('is_notarized')->default(false)->after('attestation_details');
            $table->boolean('is_translated')->default(false)->after('is_notarized');
            $table->string('status')->default('pending')->after('is_translated'); // pending, submitted, approved, rejected
            $table->text('notes')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->after('notes')->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'document_category',
                'copy_number',
                'attestation_details',
                'is_notarized',
                'is_translated',
                'status',
                'notes',
                'reviewed_by',
                'reviewed_at',
            ]);
        });
    }
};
