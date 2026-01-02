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
        Schema::table('students', function (Blueprint $table) {
            // Add missing profile fields if they don't exist
            if (!Schema::hasColumn('students', 'surname')) {
                $table->string('surname')->nullable()->after('name');
            }
            if (!Schema::hasColumn('students', 'given_names')) {
                $table->string('given_names')->nullable()->after('surname');
            }
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name')->nullable()->after('given_names');
            }
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('students', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('students', 'passport_number')) {
                $table->string('passport_number')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('students', 'passport_expiry_date')) {
                $table->date('passport_expiry_date')->nullable()->after('passport_number');
            }
            if (!Schema::hasColumn('students', 'nationality')) {
                $table->string('nationality')->nullable()->after('passport_expiry_date');
            }
            if (!Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable()->after('country');
            }
            if (!Schema::hasColumn('students', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('students', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('city');
            }
            if (!Schema::hasColumn('students', 'target_university_id')) {
                $table->foreignId('target_university_id')->nullable()->constrained('universities')->onDelete('set null')->after('course');
            }
            if (!Schema::hasColumn('students', 'target_program_id')) {
                $table->foreignId('target_program_id')->nullable()->constrained('programs')->onDelete('set null')->after('target_university_id');
            }
            if (!Schema::hasColumn('students', 'applying_program')) {
                $table->string('applying_program')->nullable()->after('target_program_id');
            }
            if (!Schema::hasColumn('students', 'highest_education')) {
                $table->string('highest_education')->nullable()->after('applying_program');
            }
            if (!Schema::hasColumn('students', 'highest_qualification')) {
                $table->string('highest_qualification')->nullable()->after('highest_education');
            }
            if (!Schema::hasColumn('students', 'previous_institution')) {
                $table->string('previous_institution')->nullable()->after('highest_qualification');
            }
            if (!Schema::hasColumn('students', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('students', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('students', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('students', 'registration_id')) {
                $table->string('registration_id')->unique()->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = [
                'surname', 'given_names', 'father_name', 'mother_name', 'password',
                'date_of_birth', 'gender', 'passport_number', 'passport_expiry_date',
                'nationality', 'address', 'city', 'postal_code', 'target_university_id',
                'target_program_id', 'applying_program', 'highest_education',
                'highest_qualification', 'previous_institution', 'emergency_contact_name',
                'emergency_contact_phone', 'emergency_contact_relationship', 'registration_id'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
