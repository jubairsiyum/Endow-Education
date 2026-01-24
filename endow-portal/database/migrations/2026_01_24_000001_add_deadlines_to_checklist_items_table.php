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
        // Add deadline columns to checklist_items table
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->date('default_deadline')->nullable()->after('order')->comment('Default deadline for all documents');
            $table->boolean('has_custom_deadlines')->default(false)->after('default_deadline')->comment('Flag if specific deadlines are set');
        });

        // Create checklist_item_deadlines table for program-specific deadlines
        Schema::create('checklist_item_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->date('deadline_date')->comment('Deadline for this document in this program');
            $table->timestamps();

            // Unique constraint to prevent duplicate deadlines for same item-program combo
            $table->unique(['checklist_item_id', 'program_id']);

            // Indexes
            $table->index('checklist_item_id');
            $table->index('program_id');
        });

        // Add deadline tracking to student_documents table
        Schema::table('student_documents', function (Blueprint $table) {
            $table->date('deadline')->nullable()->after('status')->comment('Document submission deadline');
            $table->boolean('is_overdue')->default(false)->after('deadline')->comment('Flag if submission is past deadline');
        });

        // Add deadline tracking to student_checklists table
        Schema::table('student_checklists', function (Blueprint $table) {
            $table->date('deadline')->nullable()->after('status')->comment('Document submission deadline');
            $table->boolean('is_overdue')->default(false)->after('deadline')->comment('Flag if submission is past deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'is_overdue']);
        });

        Schema::table('student_checklists', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'is_overdue']);
        });

        Schema::dropIfExists('checklist_item_deadlines');

        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropColumn(['default_deadline', 'has_custom_deadlines']);
        });
    }
};
