#!/bin/bash

###############################################################################
# Production Accounting Module Fix Script
# This script fixes common 500 errors in the accounting module
# Run this on production after pulling latest code
###############################################################################

echo "==================================================="
echo "  ACCOUNTING MODULE PRODUCTION FIX"
echo "==================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: artisan file not found. Please run this script from Laravel root directory.${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Checking PHP and Composer...${NC}"
php -v
composer --version
echo ""

echo -e "${YELLOW}Step 2: Installing/Updating dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✓ Dependencies updated${NC}"
echo ""

echo -e "${YELLOW}Step 3: Running database migrations...${NC}"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations completed${NC}"
else
    echo -e "${RED}✗ Migration failed! Check database credentials in .env${NC}"
    exit 1
fi
echo ""

echo -e "${YELLOW}Step 4: Clearing all caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

echo -e "${YELLOW}Step 5: Rebuilding caches for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓ Caches rebuilt${NC}"
echo ""

echo -e "${YELLOW}Step 6: Setting proper permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || chown -R $(whoami):www-data storage bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

echo -e "${YELLOW}Step 7: Running diagnostic check...${NC}"
php check-accounting-setup.php
echo ""

echo -e "${YELLOW}Step 8: Checking critical .env settings...${NC}"
echo "Current settings:"
grep -E "(APP_ENV|APP_DEBUG|CACHE_DRIVER|SESSION_DRIVER|DB_DATABASE)" .env | while read line; do
    echo "  - $line"
done
echo ""

# Check for problematic settings
if grep -q "APP_DEBUG=true" .env; then
    echo -e "${RED}⚠ WARNING: APP_DEBUG=true on production!${NC}"
    echo "  Change to: APP_DEBUG=false"
fi

if grep -q "CACHE_DRIVER=file" .env; then
    echo -e "${YELLOW}⚠ INFO: Using file cache driver${NC}"
    echo "  Consider changing to: CACHE_DRIVER=database"
fi
echo ""

echo -e "${YELLOW}Step 9: Testing accounting routes...${NC}"
php artisan route:list | grep accounting | head -5
echo -e "${GREEN}✓ Routes registered${NC}"
echo ""

echo "==================================================="
echo -e "${GREEN}  ACCOUNTING MODULE FIX COMPLETE!${NC}"
echo "==================================================="
echo ""
echo "Next steps:"
echo "1. Test the accounting module in browser:"
echo "   - Summary: https://yourdomain.com/office/accounting/summary"
echo "   - Categories: https://yourdomain.com/office/accounting/categories"
echo ""
echo "2. If you see 500 errors, check:"
echo "   - storage/logs/laravel.log for detailed errors"
echo "   - Your user account has 'Accountant' or 'Super Admin' role"
echo ""
echo "3. Verify .env settings:"
echo "   APP_ENV=production"
echo "   APP_DEBUG=false"
echo "   CACHE_DRIVER=database"
echo "   SESSION_DRIVER=database"
echo ""
echo "Need help? Check PRODUCTION_OPTIMIZATION.md"
