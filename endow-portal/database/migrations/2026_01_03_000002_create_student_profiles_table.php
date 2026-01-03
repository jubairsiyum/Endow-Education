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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Academic Information
            $table->string('student_id_number')->unique()->nullable();
            $table->string('academic_level')->nullable();
            $table->string('major')->nullable();
            $table->string('minor')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->date('enrollment_date')->nullable();
            $table->date('expected_graduation_date')->nullable();
            
            // Additional Profile Information
            $table->text('bio')->nullable();
            $table->text('interests')->nullable();
            $table->text('skills')->nullable();
            $table->json('languages')->nullable();
            $table->json('social_links')->nullable();
            
            // Preferences
            $table->json('preferences')->nullable();
            $table->text('profile_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('student_id_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
