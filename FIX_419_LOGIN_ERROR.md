# Fix 419 Page Expired Error on Login

## Problem
Getting "419 Page Expired" error when trying to login at https://webcms.tourliz.com/login

## Root Cause
This is a CSRF token validation failure. The CSRF token in the form doesn't match the one in the session.

## Solutions (Try in Order)

### Solution 1: Clear Cache and Sessions (MOST COMMON FIX)

SSH into your server and run:

```bash
cd /path/to/tourliz_cms
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan session:clear  # If this command exists
```

Then delete old session files:
```bash
rm -rf storage/framework/sessions/*
```

### Solution 2: Check APP_KEY

Ensure your `.env` file has a valid APP_KEY:

```bash
php artisan key:generate
php artisan config:cache
```

### Solution 3: Check Session Storage Permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage  # Replace www-data with your web server user
chown -R www-data:www-data bootstrap/cache
```

### Solution 4: Update .env File

Check your `.env` file has these settings:

```env
APP_KEY=base64:YOUR_KEY_HERE
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

For HTTPS sites, use:
```env
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none
```

### Solution 5: Check Session Configuration

Verify `config/session.php` has correct settings:

```php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'secure' => env('SESSION_SECURE_COOKIE', false),
'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

### Solution 6: Use Database Sessions (If File Sessions Don't Work)

1. Create sessions table:
```bash
php artisan session:table
php artisan migrate
```

2. Update `.env`:
```env
SESSION_DRIVER=database
```

3. Clear cache:
```bash
php artisan config:clear
php artisan config:cache
```

### Solution 7: Check Browser/Cookie Issues

1. Clear browser cookies for the domain
2. Try incognito/private mode
3. Check if cookies are being blocked
4. Verify cookie domain matches your site domain

### Solution 8: Verify Middleware Order

Ensure `StartSession` middleware runs before `VerifyCsrfToken` (should be default in Kernel.php).

## Quick Fix Script

Run this on your server:

```bash
#!/bin/bash
cd /path/to/tourliz_cms

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Regenerate key if needed
php artisan key:generate

# Fix permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Clear old sessions
rm -rf storage/framework/sessions/*

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done! Try logging in again."
```

## Files Updated

The following file was updated to fix potential session access issues:
- `app/Http/Controllers/AuthController.php` - Fixed getDeviceInfo() to check session availability

## After Fixing

1. Clear your browser cookies for the site
2. Try logging in again
3. If still failing, check Laravel logs: `storage/logs/laravel.log`
