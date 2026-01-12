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
        Schema::table('student_visits', function (Blueprint $table) {
            $table->enum('prospective_status', [
                'prospective_hot',
                'prospective_warm',
                'prospective_cold',
                'prospective_not_interested',
                'confirmed_student'
            ])->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_visits', function (Blueprint $table) {
            $table->dropColumn('prospective_status');
        });
    }
};
