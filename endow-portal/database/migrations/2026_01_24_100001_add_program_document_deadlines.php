<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration implements program-level document deadlines:
     * - Programs have a default deadline for all their documents
     * - Individual documents can override this with specific deadlines per program
     */
    public function up(): void
    {
        // Add default deadline to programs table if it doesn't already exist
        // (it may have been added by the old incorrect migration 2026_01_24_000001)
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'default_deadline')) {
                $table->date('default_deadline')->nullable()->after('order')->comment('Default deadline for all documents in this program');
            }
        });

        // Create program_document_deadlines table for document-specific deadlines per program
        if (!Schema::hasTable('program_document_deadlines')) {
            Schema::create('program_document_deadlines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained()->onDelete('cascade');
                $table->foreignId('checklist_item_id')->constrained()->onDelete('cascade');
                $table->boolean('has_specific_deadline')->default(false)->comment('Whether to use specific deadline for this document');
                $table->date('specific_deadline')->nullable()->comment('Document-specific deadline (overrides program default)');
                $table->timestamps();

                // Unique constraint to prevent duplicate entries
                $table->unique(['program_id', 'checklist_item_id']);

                // Indexes
                $table->index('program_id');
                $table->index('checklist_item_id');
            });
        }

        // Add deadline tracking to student_documents if not already added
        Schema::table('student_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('student_documents', 'deadline')) {
                $table->date('deadline')->nullable()->after('status')->comment('Applicable deadline for this document');
            }
            if (!Schema::hasColumn('student_documents', 'is_overdue')) {
                $table->boolean('is_overdue')->default(false)->after('deadline')->comment('Whether submission is past deadline');
            }
        });

        // Add deadline tracking to student_checklists if not already added
        Schema::table('student_checklists', function (Blueprint $table) {
            if (!Schema::hasColumn('student_checklists', 'deadline')) {
                $table->date('deadline')->nullable()->after('status')->comment('Applicable deadline for this checklist item');
            }
            if (!Schema::hasColumn('student_checklists', 'is_overdue')) {
                $table->boolean('is_overdue')->default(false)->after('deadline')->comment('Whether submission is past deadline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_checklists', function (Blueprint $table) {
            if (Schema::hasColumn('student_checklists', 'deadline')) {
                $table->dropColumn('deadline');
            }
            if (Schema::hasColumn('student_checklists', 'is_overdue')) {
                $table->dropColumn('is_overdue');
            }
        });

        Schema::table('student_documents', function (Blueprint $table) {
            if (Schema::hasColumn('student_documents', 'deadline')) {
                $table->dropColumn('deadline');
            }
            if (Schema::hasColumn('student_documents', 'is_overdue')) {
                $table->dropColumn('is_overdue');
            }
        });

        Schema::dropIfExists('program_document_deadlines');

        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'default_deadline')) {
                $table->dropColumn('default_deadline');
            }
        });
    }
};
