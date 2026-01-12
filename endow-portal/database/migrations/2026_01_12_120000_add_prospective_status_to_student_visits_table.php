<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('student_visits', 'prospective_status')) {
                $table->string('prospective_status')->default('prospective_warm')->after('email');
                $table->index('prospective_status');
            }
        });

        // Backfill any existing records to a safe default
        if (Schema::hasColumn('student_visits', 'prospective_status')) {
            DB::table('student_visits')
                ->whereNull('prospective_status')
                ->update(['prospective_status' => 'prospective_warm']);
        }
    }

    public function down(): void
    {
        Schema::table('student_visits', function (Blueprint $table) {
            if (Schema::hasColumn('student_visits', 'prospective_status')) {
                $table->dropIndex(['prospective_status']);
                $table->dropColumn('prospective_status');
            }
        });
    }
};
