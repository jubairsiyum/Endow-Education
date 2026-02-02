<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates report templates for recurring reports and standardization
     */
    public function up(): void
    {
        Schema::create('office_daily_report_templates', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('cascade');
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('content'); // Template content with placeholders
            
            // Template configuration (JSON)
            $table->json('fields')->nullable(); // Custom fields definition
            $table->json('default_tags')->nullable();
            
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'custom'])
                ->default('daily');
            
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mandatory')->default(false); // Must be submitted daily
            
            $table->integer('usage_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('department_id');
            $table->index(['is_active', 'is_mandatory']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_report_templates');
    }
};
