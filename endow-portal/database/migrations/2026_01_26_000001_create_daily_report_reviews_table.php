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
        Schema::create('office_daily_report_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('office_daily_reports')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->boolean('marked_as_completed')->default(false);
            $table->timestamp('reviewed_at');
            $table->timestamps();

            // Indexes for better performance
            $table->index('daily_report_id');
            $table->index('reviewer_id');
            $table->index('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_daily_report_reviews');
    }
};
