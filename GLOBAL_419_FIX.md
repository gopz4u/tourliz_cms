# GLOBAL 419 ERROR FIX - Everywhere

## Problem
419 Page Expired error occurring on ALL pages/forms, not just login.

## Root Cause
This indicates a **fundamental session/CSRF configuration issue** on the server. Sessions are not being maintained properly, causing CSRF token validation to fail everywhere.

## IMMEDIATE SERVER FIX (CRITICAL)

### Step 1: Update .env File

**SSH into your server and edit `.env`:**

```bash
cd /path/to/tourliz_cms
nano .env
```

**Add/Update these EXACT settings:**

```env
# Application
APP_NAME="Tourliz CMS"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=https://webcms.tourliz.com

# Session Configuration - CRITICAL
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none

# Cookie Configuration
COOKIE_DOMAIN=null
```

### Step 2: Run These Commands

```bash
# 1. Clear ALL caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 2. Delete ALL old sessions
rm -rf storage/framework/sessions/*

# 3. Fix permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# 4. Ensure session directory exists
mkdir -p storage/framework/sessions
chmod 775 storage/framework/sessions

# 5. Regenerate APP_KEY
php artisan key:generate --force

# 6. Rebuild config cache
php artisan config:cache
```

### Step 3: Restart Web Server

```bash
# For Apache
sudo systemctl restart apache2
# or
sudo service apache2 restart

# For Nginx + PHP-FPM
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

## Alternative: Use Database Sessions

If file sessions still don't work:

```bash
# Create sessions table
php artisan session:table
php artisan migrate

# Update .env
SESSION_DRIVER=database

# Clear and rebuild
php artisan config:clear
php artisan config:cache
```

## Verify Fix

### Test 1: Check Session is Working
```bash
php artisan tinker
>>> session(['test' => 'working']);
=> null
>>> session('test');
=> "working"
>>> exit
```

### Test 2: Check Config Values
```bash
php artisan tinker
>>> config('session.secure');
=> true
>>> config('session.same_site');
=> "none"
>>> config('session.driver');
=> "file"
>>> exit
```

### Test 3: Check Browser Cookies

1. Open browser DevTools (F12)
2. Go to Application/Storage → Cookies
3. Visit: `https://webcms.tourliz.com/login`
4. You should see:
   - `laravel_session` cookie (Secure: ✓, SameSite: None)
   - `XSRF-TOKEN` cookie (Secure: ✓, SameSite: None)

## Code Changes Applied

1. **VerifyCsrfToken Middleware** - Improved session initialization
2. **Admin Layout** - Auto-refreshing CSRF tokens for AJAX
3. **Session Config** - Auto-detects HTTPS and sets correct cookie settings

## Files to Upload

1. `app/Http/Middleware/VerifyCsrfToken.php`
2. `config/session.php`
3. `resources/views/layouts/admin.blade.php`
4. `app/Http/Controllers/AuthController.php`

## If Still Not Working

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

Look for:
- "Failed to start session"
- "CSRF token missing"
- Session-related errors

### Check PHP Session Settings
```bash
php -i | grep session
```

Ensure:
- `session.save_path` is writable
- `session.cookie_secure` = 1 (for HTTPS)
- `session.cookie_samesite` = None (for HTTPS)

### Check Web Server Logs
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

## Most Common Issue

**The `.env` file on the server is NOT updated with the correct session settings!**

Without these in `.env`:
- `SESSION_SECURE_COOKIE=true`
- `SESSION_SAME_SITE=none`

The session cookies will NOT work on HTTPS, causing 419 errors everywhere.

## Quick Diagnostic

Run this on your server to check current config:

```bash
php artisan tinker --execute="
echo 'Session Driver: ' . config('session.driver') . PHP_EOL;
echo 'Session Secure: ' . (config('session.secure') ? 'true' : 'false') . PHP_EOL;
echo 'Session SameSite: ' . config('session.same_site') . PHP_EOL;
echo 'Session Domain: ' . (config('session.domain') ?: 'null') . PHP_EOL;
echo 'APP_URL: ' . config('app.url') . PHP_EOL;
"
```

If any of these are wrong, update `.env` and run `php artisan config:clear && php artisan config:cache`.
