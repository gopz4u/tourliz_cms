#!/bin/bash

# Fix 419 Page Expired Error - Server Script
# Run this script on your server to fix common 419 errors

echo "=========================================="
echo "Fixing 419 Page Expired Error"
echo "=========================================="

# Get current directory
PROJECT_DIR=$(pwd)
echo "Project Directory: $PROJECT_DIR"

# Step 1: Generate APP_KEY if missing
echo ""
echo "Step 1: Checking APP_KEY..."
if grep -q "APP_KEY=$" .env 2>/dev/null || ! grep -q "APP_KEY=" .env 2>/dev/null; then
    echo "APP_KEY is missing or empty. Generating new key..."
    php artisan key:generate
else
    echo "APP_KEY is set."
fi

# Step 2: Set permissions
echo ""
echo "Step 2: Setting file permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
echo "Permissions set."

# Step 3: Create session directory if missing
echo ""
echo "Step 3: Checking session directory..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage/framework
echo "Session directory ready."

# Step 4: Clear all caches
echo ""
echo "Step 4: Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo "Caches cleared."

# Step 5: Rebuild caches
echo ""
echo "Step 5: Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "Caches rebuilt."

# Step 6: Check .env configuration
echo ""
echo "Step 6: Checking .env configuration..."
if [ -f .env ]; then
    echo "✓ .env file exists"
    
    if grep -q "SESSION_DRIVER=" .env; then
        echo "✓ SESSION_DRIVER is set"
    else
        echo "⚠ SESSION_DRIVER not found, adding default..."
        echo "SESSION_DRIVER=file" >> .env
    fi
    
    if grep -q "SESSION_LIFETIME=" .env; then
        echo "✓ SESSION_LIFETIME is set"
    else
        echo "⚠ SESSION_LIFETIME not found, adding default..."
        echo "SESSION_LIFETIME=120" >> .env
    fi
else
    echo "✗ .env file not found! Please create it."
fi

echo ""
echo "=========================================="
echo "Fix Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Verify APP_KEY is set in .env"
echo "2. Check SESSION_DRIVER=file in .env"
echo "3. Clear browser cache and cookies"
echo "4. Test the application"
echo ""

