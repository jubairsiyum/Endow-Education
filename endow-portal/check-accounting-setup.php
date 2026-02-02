#!/usr/bin/env php
<?php

/**
 * Accounting Module Diagnostic Script
 * Run this on production to identify setup issues
 * Usage: php check-accounting-setup.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=================================================\n";
echo "   ACCOUNTING MODULE DIAGNOSTIC CHECK\n";
echo "=================================================\n\n";

// 1. Check Database Connection
echo "1. Checking Database Connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Database connected successfully\n";
    echo "   - Database: " . DB::connection()->getDatabaseName() . "\n";
    echo "   - Driver: " . DB::connection()->getDriverName() . "\n\n";
} catch (\Exception $e) {
    echo "   ✗ Database connection FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Check Required Tables
echo "2. Checking Required Tables...\n";
$requiredTables = [
    'account_categories',
    'transactions',
    'users',
    'roles',
    'permissions'
];

$missingTables = [];
foreach ($requiredTables as $table) {
    if (Schema::hasTable($table)) {
        echo "   ✓ Table '$table' exists\n";
        
        // Show row count
        $count = DB::table($table)->count();
        echo "     - Rows: $count\n";
    } else {
        echo "   ✗ Table '$table' is MISSING\n";
        $missingTables[] = $table;
    }
}
echo "\n";

if (!empty($missingTables)) {
    echo "⚠ MISSING TABLES DETECTED!\n";
    echo "  Run these commands on production:\n";
    echo "  1. php artisan migrate --force\n";
    echo "  2. php artisan db:seed --class=RolesAndPermissionsSeeder --force\n\n";
}

// 3. Check Table Structures
if (Schema::hasTable('account_categories')) {
    echo "3. Checking 'account_categories' Table Structure...\n";
    $columns = Schema::getColumnListing('account_categories');
    $requiredColumns = ['id', 'name', 'type', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'];
    
    foreach ($requiredColumns as $col) {
        if (in_array($col, $columns)) {
            echo "   ✓ Column '$col' exists\n";
        } else {
            echo "   ✗ Column '$col' is MISSING\n";
        }
    }
    echo "\n";
}

if (Schema::hasTable('transactions')) {
    echo "4. Checking 'transactions' Table Structure...\n";
    $columns = Schema::getColumnListing('transactions');
    $requiredColumns = [
        'id', 'account_category_id', 'headline', 'amount', 'currency', 
        'entry_date', 'type', 'status', 'created_by', 'employee_id',
        'approved_by', 'approved_at', 'created_at', 'updated_at', 'deleted_at'
    ];
    
    foreach ($requiredColumns as $col) {
        if (in_array($col, $columns)) {
            echo "   ✓ Column '$col' exists\n";
        } else {
            echo "   ✗ Column '$col' is MISSING\n";
        }
    }
    echo "\n";
}

// 5. Check Migrations Status
echo "5. Checking Migrations Status...\n";
try {
    $migrations = DB::table('migrations')
        ->where('migration', 'like', '%account_categories%')
        ->orWhere('migration', 'like', '%transactions%')
        ->get(['migration', 'batch']);
    
    if ($migrations->count() > 0) {
        echo "   ✓ Accounting migrations found:\n";
        foreach ($migrations as $migration) {
            echo "     - {$migration->migration} (batch {$migration->batch})\n";
        }
    } else {
        echo "   ✗ No accounting migrations found in migrations table\n";
        echo "   ⚠ Run: php artisan migrate --force\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error checking migrations: " . $e->getMessage() . "\n\n";
}

// 6. Check Roles and Permissions
echo "6. Checking Roles and Permissions...\n";
try {
    if (Schema::hasTable('roles')) {
        $accountantRole = DB::table('roles')->where('name', 'Accountant')->first();
        if ($accountantRole) {
            echo "   ✓ 'Accountant' role exists (ID: {$accountantRole->id})\n";
        } else {
            echo "   ✗ 'Accountant' role NOT found\n";
            echo "   ⚠ Run: php artisan db:seed --class=RolesAndPermissionsSeeder --force\n";
        }
    }
    
    if (Schema::hasTable('permissions')) {
        $accountingPermissions = DB::table('permissions')
            ->where('name', 'like', '%transaction%')
            ->orWhere('name', 'like', '%accounting%')
            ->get(['name']);
        
        if ($accountingPermissions->count() > 0) {
            echo "   ✓ Found {$accountingPermissions->count()} accounting permissions:\n";
            foreach ($accountingPermissions as $perm) {
                echo "     - {$perm->name}\n";
            }
        } else {
            echo "   ⚠ No accounting permissions found\n";
        }
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error checking roles/permissions: " . $e->getMessage() . "\n\n";
}

// 7. Check Cache Configuration
echo "7. Checking Cache Configuration...\n";
echo "   - CACHE_DRIVER: " . env('CACHE_DRIVER', 'file') . "\n";
echo "   - SESSION_DRIVER: " . env('SESSION_DRIVER', 'file') . "\n";

if (env('CACHE_DRIVER') === 'file' || env('SESSION_DRIVER') === 'file') {
    echo "   ⚠ WARNING: Using 'file' cache/session driver on production\n";
    echo "   Recommendation: Use 'database' or 'redis' for better performance\n";
    echo "   Update .env:\n";
    echo "     CACHE_DRIVER=database\n";
    echo "     SESSION_DRIVER=database\n";
    echo "   Then run: php artisan cache:clear && php artisan config:cache\n";
}
echo "\n";

// 8. Test Model Loading
echo "8. Testing Model Loading...\n";
try {
    $categoryCount = App\Models\AccountCategory::count();
    echo "   ✓ AccountCategory model loads successfully ($categoryCount records)\n";
} catch (\Exception $e) {
    echo "   ✗ AccountCategory model error: " . $e->getMessage() . "\n";
}

try {
    $transactionCount = App\Models\Transaction::count();
    echo "   ✓ Transaction model loads successfully ($transactionCount records)\n";
} catch (\Exception $e) {
    echo "   ✗ Transaction model error: " . $e->getMessage() . "\n";
}
echo "\n";

// 9. Check Routes
echo "9. Checking Routes Registration...\n";
try {
    $accountingRoutes = collect(Route::getRoutes())->filter(function ($route) {
        return str_contains($route->getName(), 'accounting');
    });
    
    echo "   ✓ Found " . $accountingRoutes->count() . " accounting routes\n";
    if ($accountingRoutes->count() === 0) {
        echo "   ⚠ No accounting routes found. Check routes/web.php\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

// 10. Environment Check
echo "10. Environment Configuration...\n";
echo "   - APP_ENV: " . env('APP_ENV', 'production') . "\n";
echo "   - APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "   - APP_URL: " . env('APP_URL', 'not set') . "\n";

if (env('APP_DEBUG') && env('APP_ENV') === 'production') {
    echo "   ⚠ WARNING: APP_DEBUG is enabled on production!\n";
    echo "   Set APP_DEBUG=false in .env for security\n";
}
echo "\n";

echo "=================================================\n";
echo "   DIAGNOSTIC CHECK COMPLETE\n";
echo "=================================================\n";

if (!empty($missingTables)) {
    echo "\n❌ SETUP INCOMPLETE - Missing tables detected\n";
    echo "Action Required: Run migrations on production\n";
    exit(1);
} else {
    echo "\n✅ All basic checks passed\n";
    echo "If you're still seeing 500 errors, check:\n";
    echo "  1. storage/logs/laravel.log for detailed errors\n";
    echo "  2. User roles/permissions are assigned correctly\n";
    echo "  3. Run: php artisan config:cache && php artisan route:cache\n";
    exit(0);
}
