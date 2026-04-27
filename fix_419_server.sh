#!/bin/bash

# Fix 419 Error - Server Script
# Run this script on your server to fix the 419 Page Expired error

echo "=========================================="
echo "Fixing 419 Page Expired Error"
echo "=========================================="

# Navigate to project directory
cd /path/to/tourliz_cms || exit 1

echo "Step 1: Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

echo "Step 2: Clearing old sessions..."
rm -rf storage/framework/sessions/*

echo "Step 3: Fixing permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage 2>/dev/null || chown -R apache:apache storage 2>/dev/null || echo "Note: Update ownership manually"
chown -R www-data:www-data bootstrap/cache 2>/dev/null || chown -R apache:apache bootstrap/cache 2>/dev/null || echo "Note: Update ownership manually"

echo "Step 4: Ensuring session directory exists..."
mkdir -p storage/framework/sessions
chmod 775 storage/framework/sessions

echo "Step 5: Regenerating APP_KEY..."
php artisan key:generate --force

echo "Step 6: Rebuilding config cache..."
php artisan config:cache

echo "=========================================="
echo "Fix complete!"
echo "=========================================="
echo ""
echo "IMPORTANT: Make sure your .env file has:"
echo "  SESSION_SECURE_COOKIE=true"
echo "  SESSION_SAME_SITE=none"
echo "  SESSION_DOMAIN=null"
echo ""
echo "Then run: php artisan config:clear && php artisan config:cache"
