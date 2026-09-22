#!/bin/bash
# ================================================================================
# Tourliz CMS — Automated Hostinger Deployment Script
# ================================================================================

echo "🚀 Starting Tourliz CMS Hostinger Deployment..."

# 1. Unzip update package if present
if [ -f "tourliz_update_hostinger.zip" ]; then
    echo "📦 Extracting tourliz_update_hostinger.zip..."
    unzip -o tourliz_update_hostinger.zip
    echo "✅ Extraction complete."
fi

# 2. Set Directory and File Permissions
echo "🔒 Setting file and storage permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public

# 3. Create Storage Symlink if missing
if [ ! -L "public/storage" ] && [ ! -d "public/storage" ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

# 4. Execute Database Migrations
echo "🗄️ Running pending database migrations..."
php artisan migrate --force

# 5. Clear and Rebuild Laravel Caches
echo "🧹 Clearing and rebuilding application caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache

echo "🎉 Tourliz CMS Hostinger Deployment Successfully Completed!"
