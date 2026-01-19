<?php
/**
 * Emergency fix script for student_visits prospective_status column issue
 * Run this directly on production server if migration fails
 *
 * Usage: php fix-student-visits-production.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "================================================\n";
echo "Student Visits - Emergency Production Fix\n";
echo "================================================\n\n";

try {
    // Step 1: Check column type and convert if needed
    echo "Step 1: Checking column type...\n";
    $columnExists = Schema::hasColumn('student_visits', 'prospective_status');

    if (!$columnExists) {
        echo "  -> Column does not exist. Adding it...\n";
        DB::statement("ALTER TABLE student_visits ADD COLUMN prospective_status VARCHAR(50) NULL AFTER email");
        echo "  ✓ Column added successfully\n\n";
    } else {
        // Check if column is ENUM and convert to VARCHAR
        $columnInfo = DB::select("SHOW COLUMNS FROM student_visits WHERE Field = 'prospective_status'");
        if (!empty($columnInfo)) {
            $columnType = $columnInfo[0]->Type;
            echo "  -> Current type: {$columnType}\n";

            if (strpos($columnType, 'enum') !== false) {
                echo "  -> Converting ENUM to VARCHAR to allow data update...\n";
                DB::statement("ALTER TABLE student_visits MODIFY COLUMN prospective_status VARCHAR(50) NULL");
                echo "  ✓ Column converted to VARCHAR\n\n";
            } else {
                echo "  ✓ Column type is already correct\n\n";
            }
        }
    }

    // Step 2: Update invalid data (now that column is VARCHAR)
    echo "Step 2: Cleaning up invalid data...\n";

    // Use a safer approach - update null values first
    $nullUpdated = DB::table('student_visits')
        ->whereNull('prospective_status')
        ->update(['prospective_status' => 'prospective_warm']);
    echo "  -> Updated {$nullUpdated} NULL values\n";

    // Update empty strings
    $emptyUpdated = DB::table('student_visits')
        ->where('prospective_status', '')
        ->update(['prospective_status' => 'prospective_warm']);
    echo "  -> Updated {$emptyUpdated} empty values\n";

    // Update invalid values (not in our list)
    $invalidUpdated = DB::table('student_visits')
        ->whereNotIn('prospective_status', [
            'prospective_hot',
            'prospective_warm',
            'prospective_cold',
            'prospective_not_interested',
            'confirmed_student'
        ])
        ->update(['prospective_status' => 'prospective_warm']);
    echo "  -> Updated {$invalidUpdated} invalid values\n";

    $totalUpdated = $nullUpdated + $emptyUpdated + $invalidUpdated;
    echo "  ✓ Total updated: {$totalUpdated} rows\n\n";

    // Step 3: Set default value
    echo "Step 3: Setting default value for column...\n";
    DB::statement("ALTER TABLE student_visits MODIFY COLUMN prospective_status VARCHAR(50) NOT NULL DEFAULT 'prospective_warm'");
    echo "  ✓ Default value set\n\n";

    // Step 4: Add index if not exists
    echo "Step 4: Adding index for better performance...\n";
    try {
        DB::statement("CREATE INDEX idx_prospective_status ON student_visits(prospective_status)");
        echo "  ✓ Index created\n\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "  → Index already exists\n\n";
        } else {
            throw $e;
        }
    }

    // Step 5: Mark failed migration as successful
    echo "Step 5: Fixing migrations table...\n";

    // Check if the failed migration exists in migrations table
    $failedMigration = DB::table('migrations')
        ->where('migration', 'LIKE', '%fix_prospective_status_column_in_student_visits_table')
        ->first();

    if ($failedMigration) {
        // Delete the failed entry
        DB::table('migrations')
            ->where('migration', 'LIKE', '%fix_prospective_status_column_in_student_visits_table')
            ->delete();
        echo "  ✓ Removed failed migration entry\n\n";
    }

    // Add the safe migration if it doesn't exist
    $safeMigration = DB::table('migrations')
        ->where('migration', '2026_01_12_140000_safe_add_prospective_status_column')
        ->first();

    if (!$safeMigration) {
        DB::table('migrations')->insert([
            'migration' => '2026_01_12_140000_safe_add_prospective_status_column',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "  ✓ Added safe migration entry\n\n";
    }

    // Step 6: Verify the fix
    echo "Step 6: Verifying the fix...\n";
    $count = DB::table('student_visits')->count();
    $validCount = DB::table('student_visits')
        ->whereIn('prospective_status', [
            'prospective_hot',
            'prospective_warm',
            'prospective_cold',
            'prospective_not_interested',
            'confirmed_student'
        ])
        ->count();

    echo "  Total visits: {$count}\n";
    echo "  Valid status values: {$validCount}\n";

    if ($count === $validCount) {
        echo "  ✓ All data is valid!\n\n";
    } else {
        echo "  ⚠ Warning: Some records still have invalid values\n\n";
    }

    echo "================================================\n";
    echo "✓ Fix completed successfully!\n";
    echo "================================================\n\n";

    echo "Next steps:\n";
    echo "1. Clear caches: php artisan config:clear && php artisan cache:clear && php artisan view:clear\n";
    echo "2. Test the pages: /student-visits, /student-visits/create, /student-visits/edit\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    exit(1);
}
