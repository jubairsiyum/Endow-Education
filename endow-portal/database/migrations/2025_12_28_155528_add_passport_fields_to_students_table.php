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
            $table->string('surname')->nullable()->after('name');
            $table->string('given_names')->nullable()->after('surname');
            $table->string('father_name')->nullable()->after('given_names');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->date('date_of_birth')->nullable()->after('mother_name');
            $table->string('passport_number')->nullable()->after('date_of_birth');
            $table->string('nationality')->nullable()->after('country');
            $table->string('applying_program')->nullable()->after('course');
            $table->string('highest_education')->nullable()->after('applying_program');
            $table->string('address')->nullable()->after('highest_education');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'surname', 'given_names', 'father_name', 'mother_name',
                'date_of_birth', 'passport_number', 'nationality',
                'applying_program', 'highest_education', 'address',
                'city', 'postal_code'
            ]);
        });
    }
};
