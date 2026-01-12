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
            // SSC (Secondary School Certificate)
            $table->string('ssc_year', 4)->nullable()->after('emergency_contact_relationship');
            $table->string('ssc_result', 20)->nullable()->after('ssc_year');

            // HSC (Higher Secondary Certificate)
            $table->string('hsc_year', 4)->nullable()->after('ssc_result');
            $table->string('hsc_result', 20)->nullable()->after('hsc_year');

            // IELTS
            $table->boolean('has_ielts')->default(false)->after('hsc_result');
            $table->string('ielts_score', 10)->nullable()->after('has_ielts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'ssc_year',
                'ssc_result',
                'hsc_year',
                'hsc_result',
                'has_ielts',
                'ielts_score'
            ]);
        });
    }
};
