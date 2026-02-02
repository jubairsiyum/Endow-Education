#!/bin/bash

# PRODUCTION DEPLOYMENT SCRIPT FOR ACCOUNTING MODULE FIX
# Run this on your production server after pushing the code

echo "🚀 Starting Production Optimization..."
echo ""

# Step 1: Pull latest code
echo "📥 Step 1: Pulling latest code..."
git pull origin main
echo "✓ Code updated"
echo ""

# Step 2: Install/Update dependencies
echo "📦 Step 2: Updating dependencies..."
composer install --no-dev --optimize-autoloader
echo "✓ Dependencies updated"
echo ""

# Step 3: Run database migrations (adds indexes)
echo "🗄️  Step 3: Running migrations (adding performance indexes)..."
php artisan migrate --force
echo "✓ Indexes added"
echo ""

# Step 4: Create cache tables if not exist
echo "📊 Step 4: Setting up cache tables..."
php artisan cache:table 2>/dev/null || echo "Cache table already exists"
php artisan session:table 2>/dev/null || echo "Session table already exists"
php artisan migrate --force
echo "✓ Cache tables ready"
echo ""

# Step 5: Clear all caches
echo "🧹 Step 5: Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo "✓ All caches cleared"
echo ""

# Step 6: Optimize for production
echo "⚡ Step 6: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo "✓ Application optimized"
echo ""

# Step 7: Set permissions
echo "🔒 Step 7: Setting permissions..."
chmod -R 755 storage bootstrap/cache
echo "✓ Permissions set"
echo ""

# Step 8: Verify critical settings
echo "✅ Step 8: Verifying settings..."
echo ""
echo "Checking APP_ENV..."
grep "APP_ENV" .env
echo ""
echo "Checking APP_DEBUG..."
grep "APP_DEBUG" .env
echo ""
echo "Checking CACHE_DRIVER..."
grep "CACHE_DRIVER" .env
echo ""

echo "========================================="
echo "✨ DEPLOYMENT COMPLETE!"
echo "========================================="
echo ""
echo "⚠️  IMPORTANT: Make sure your .env has:"
echo "   - APP_ENV=production"
echo "   - APP_DEBUG=false"
echo "   - CACHE_DRIVER=database"
echo "   - SESSION_DRIVER=database"
echo ""
echo "📝 Test your website now!"
echo ""
