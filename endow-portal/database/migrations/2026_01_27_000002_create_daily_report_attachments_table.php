<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates table for report attachments (documents, images, etc.)
     */
    public function up(): void
    {
        Schema::create('office_daily_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                ->constrained('office_daily_reports')
                ->onDelete('cascade');
            
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 50); // pdf, docx, xlsx, image, etc.
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('mime_type', 100);
            
            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes
            $table->index('daily_report_id');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_report_attachments');
    }
};
