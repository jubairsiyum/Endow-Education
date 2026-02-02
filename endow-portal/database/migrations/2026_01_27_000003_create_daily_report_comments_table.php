<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates collaborative comments system for reports
     * Allows multiple stakeholders to discuss and provide feedback
     */
    public function up(): void
    {
        Schema::create('office_daily_report_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                ->constrained('office_daily_reports')
                ->onDelete('cascade');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->text('comment');
            
            // For threaded comments
            $table->foreignId('parent_comment_id')
                ->nullable()
                ->constrained('office_daily_report_comments')
                ->onDelete('cascade');
            
            // Comment type: feedback, question, approval, rejection, note
            $table->enum('type', ['feedback', 'question', 'approval', 'rejection', 'note'])
                ->default('feedback');
            
            $table->boolean('is_internal')->default(false); // Internal notes vs visible to submitter
            $table->boolean('is_read')->default(false);
            
            $table->timestamps();
            $table->softDeletes(); // Allow comment deletion
            
            // Indexes
            $table->index('daily_report_id');
            $table->index(['daily_report_id', 'created_at']);
            $table->index('parent_comment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_report_comments');
    }
};
