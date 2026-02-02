# Accounting Module 500 Error Troubleshooting Guide

## Common Causes of 500 Errors

### 1. Database Tables Not Created
**Symptoms:** "Table 'account_categories' doesn't exist" in logs

**Fix:**
```bash
# On production server
cd /path/to/your/project
php artisan migrate --force
```

Check if migrations ran successfully:
```bash
php artisan migrate:status | grep account
php artisan migrate:status | grep transaction
```

### 2. Missing Permissions/Roles
**Symptoms:** Access denied or blank pages

**Fix:**
```bash
# Create/update roles and permissions
php artisan db:seed --class=RolesAndPermissionsSeeder --force

# Or create manually in database
INSERT INTO roles (name, guard_name, created_at, updated_at) 
VALUES ('Accountant', 'web', NOW(), NOW());

# Assign role to user
INSERT INTO model_has_roles (role_id, model_type, model_id) 
VALUES (
    (SELECT id FROM roles WHERE name='Accountant'), 
    'App\\Models\\User', 
    YOUR_USER_ID
);
```

### 3. Cache Issues
**Symptoms:** Old routes, config not updating

**Fix:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 4. Database Connection Issues
**Symptoms:** "SQLSTATE[HY000] [2002]" errors

**Check .env file:**
```bash
DB_CONNECTION=mysql
DB_HOST=srv1749.hstgr.io
DB_PORT=3306
DB_DATABASE=u523324533_endoweducation
DB_USERNAME=u523324533_endoweducation
DB_PASSWORD=your_password
```

**Test connection:**
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::select('SELECT 1');
```

### 5. Foreign Key Constraints
**Symptoms:** "Cannot add or update a child row: a foreign key constraint fails"

**Fix:** Ensure related tables exist and have data:
```bash
php artisan tinker
>>> Schema::hasTable('account_categories');  // Should return true
>>> Schema::hasTable('transactions');         // Should return true
>>> Schema::hasTable('users');                // Should return true
```

### 6. File Permissions
**Symptoms:** "Permission denied" in logs

**Fix:**
```bash
# Set correct permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Or for shared hosting
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;
```

## Diagnostic Steps

### Step 1: Check Laravel Logs
```bash
# View recent errors
tail -n 100 storage/logs/laravel.log

# Search for specific errors
grep "accounting" storage/logs/laravel.log
grep "500" storage/logs/laravel.log
```

### Step 2: Run Diagnostic Script
```bash
php check-accounting-setup.php
```

This will check:
- Database connection
- Required tables existence
- Table structures
- Migrations status
- Roles and permissions
- Model loading
- Routes registration

### Step 3: Test in Tinker
```bash
php artisan tinker

# Test models
>>> App\Models\AccountCategory::count();
>>> App\Models\Transaction::count();

# Test query
>>> App\Models\AccountCategory::all();

# Check relationships
>>> $transaction = App\Models\Transaction::first();
>>> $transaction->category;
>>> $transaction->creator;
```

### Step 4: Check Apache/Nginx Error Logs
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log

# Hostinger (cPanel)
# Access via cPanel > Error Log viewer
```

## Quick Fixes for Specific Pages

### Summary Dashboard (/office/accounting/summary)
**Common Issues:**
1. Missing transactions table → Run migrations
2. No approved transactions → Check if transactions exist in database
3. Category relationship error → Check foreign keys

**Debug:**
```php
// Add to TransactionController@summary temporarily
dd([
    'table_exists' => Schema::hasTable('transactions'),
    'transaction_count' => Transaction::count(),
    'approved_count' => Transaction::approved()->count(),
]);
```

### Categories Page (/office/accounting/categories)
**Common Issues:**
1. Missing account_categories table → Run migrations
2. Stats calculation error → Check table has data

**Debug:**
```php
// Add to AccountCategoryController@index temporarily
dd([
    'table_exists' => Schema::hasTable('account_categories'),
    'category_count' => AccountCategory::count(),
    'categories' => AccountCategory::all(),
]);
```

## Production Server Checklist

### Environment Configuration
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` (CRITICAL for performance and security)
- [ ] `CACHE_DRIVER=database` (not file)
- [ ] `SESSION_DRIVER=database` (not file)
- [ ] Database credentials correct
- [ ] `APP_URL` matches your domain

### Database
- [ ] `account_categories` table exists
- [ ] `transactions` table exists
- [ ] Foreign keys properly created
- [ ] Has at least 1 category seeded
- [ ] Migrations table has accounting entries

### Permissions
- [ ] storage/ directory writable (775)
- [ ] bootstrap/cache/ writable (775)
- [ ] .env file not publicly accessible

### Application
- [ ] Composer dependencies installed
- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`
- [ ] Views cached: `php artisan view:cache`

### User Access
- [ ] User has 'Accountant' or 'Super Admin' role
- [ ] Necessary permissions assigned
- [ ] User can access /office routes

## Emergency Recovery Commands

If accounting module is completely broken:

```bash
# 1. Backup database first!
php artisan db:backup  # If available

# 2. Rollback and re-run migrations
php artisan migrate:rollback --step=3
php artisan migrate --force

# 3. Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 4. Rebuild
composer dump-autoload
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Test
php check-accounting-setup.php
```

## Getting Detailed Error Information

### Enable Debug Mode Temporarily (Production)
**⚠️ WARNING: Only do this for few minutes, then disable!**

```bash
# In .env
APP_DEBUG=true

# Test the page to see full error

# IMMEDIATELY after seeing error:
APP_DEBUG=false
php artisan config:cache
```

### Check PHP Error Log
```bash
# Find PHP error log location
php -i | grep error_log

# View errors
tail -f /path/to/php-errors.log
```

## Contact Support Information

If issue persists after trying all fixes:

1. **Collect this information:**
   - Laravel version: `php artisan --version`
   - PHP version: `php -v`
   - Error from storage/logs/laravel.log
   - Output of: `php check-accounting-setup.php`
   - Screenshot of error page (with APP_DEBUG=true)

2. **Check GitHub Issues:**
   https://github.com/jubairsiyum/Endow-Education/issues

3. **Create detailed issue report** with all information from step 1.

## Prevention Checklist

To prevent future 500 errors:

- [ ] Always run migrations after git pull: `php artisan migrate --force`
- [ ] Always clear cache after updates: `php artisan optimize:clear`
- [ ] Test in local/staging before production
- [ ] Keep APP_DEBUG=false on production
- [ ] Monitor storage/logs/laravel.log regularly
- [ ] Use database cache driver for better performance
- [ ] Ensure all foreign key dependencies exist
- [ ] Backup database before major changes

## Performance Tips

After fixing errors, optimize:

```bash
# Create database cache table
php artisan cache:table
php artisan session:table
php artisan migrate --force

# Update .env
CACHE_DRIVER=database
SESSION_DRIVER=database

# Clear and rebuild
php artisan optimize:clear
php artisan optimize

# Run diagnostic
php check-accounting-setup.php
```

Expected improvements:
- Page load: 5-15s → 0.5-2s
- Database queries: 50-100 → 5-15
- Pending count: Real-time → Cached 60s
