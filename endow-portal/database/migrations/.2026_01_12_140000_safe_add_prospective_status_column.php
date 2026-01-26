<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if the column already exists
        if (!Schema::hasColumn('student_visits', 'prospective_status')) {
            // Add the column as nullable varchar first
            Schema::table('student_visits', function (Blueprint $table) {
                $table->string('prospective_status')->nullable()->after('email');
            });
        }

        // Update any existing rows with invalid or null values to a default
        DB::statement("
            UPDATE student_visits
            SET prospective_status = 'prospective_warm'
            WHERE prospective_status IS NULL
               OR prospective_status = ''
               OR prospective_status NOT IN (
                   'prospective_hot',
                   'prospective_warm',
                   'prospective_cold',
                   'prospective_not_interested',
                   'confirmed_student'
               )
        ");

        // Now set a default value for new records
        Schema::table('student_visits', function (Blueprint $table) {
            $table->string('prospective_status')->default('prospective_warm')->change();
        });

        // Add index for better query performance
        if (!Schema::hasIndex('student_visits', ['prospective_status'])) {
            Schema::table('student_visits', function (Blueprint $table) {
                $table->index('prospective_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_visits', function (Blueprint $table) {
            if (Schema::hasIndex('student_visits', ['prospective_status'])) {
                $table->dropIndex(['prospective_status']);
            }
            if (Schema::hasColumn('student_visits', 'prospective_status')) {
                $table->dropColumn('prospective_status');
            }
        });
    }
};
