<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration rolls back the incorrect deadline implementation from 2026_01_24_000001
     * and ensures we're using the correct program-level deadline system instead.
     */
    public function up(): void
    {
        // Drop the incorrect checklist_item_deadlines table
        Schema::dropIfExists('checklist_item_deadlines');

        // Remove deadline columns from checklist_items table
        Schema::table('checklist_items', function (Blueprint $table) {
            if (Schema::hasColumn('checklist_items', 'default_deadline')) {
                $table->dropColumn('default_deadline');
            }
            if (Schema::hasColumn('checklist_items', 'has_custom_deadlines')) {
                $table->dropColumn('has_custom_deadlines');
            }
        });

        // Note: We keep deadline and is_overdue columns in student_documents and student_checklists
        // for backward compatibility, but they are now calculated from the program's deadline settings
        // via the StudentChecklist::getApplicableDeadline() method
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back deadline columns to checklist_items table
        Schema::table('checklist_items', function (Blueprint $table) {
            if (!Schema::hasColumn('checklist_items', 'default_deadline')) {
                $table->date('default_deadline')->nullable()->after('order')->comment('Default deadline for all documents');
            }
            if (!Schema::hasColumn('checklist_items', 'has_custom_deadlines')) {
                $table->boolean('has_custom_deadlines')->default(false)->after('default_deadline')->comment('Flag if specific deadlines are set');
            }
        });

        // Recreate checklist_item_deadlines table
        Schema::create('checklist_item_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->date('deadline_date')->comment('Deadline for this document in this program');
            $table->timestamps();

            $table->unique(['checklist_item_id', 'program_id']);
            $table->index('checklist_item_id');
            $table->index('program_id');
        });
    }
};
