# Fix 419 Page Expired Error - Server Instructions

## Quick Fix Steps for Server

### Step 1: Clear All Caches
```bash
cd /path/to/tourliz_cms
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan session:clear  # If available
```

### Step 2: Clear Old Sessions
```bash
rm -rf storage/framework/sessions/*
```

### Step 3: Fix Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage  # Replace www-data with your web server user
chown -R www-data:www-data bootstrap/cache
```

### Step 4: Verify APP_KEY
```bash
php artisan key:generate
php artisan config:cache
```

### Step 5: Check .env File
Ensure these settings in `.env`:
```env
APP_KEY=base64:YOUR_KEY_HERE
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

For HTTPS sites:
```env
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none
```

### Step 6: Upload Updated Files
Upload the updated `app/Http/Controllers/AuthController.php` file that ensures session is started.

### Step 7: Test
1. Clear browser cookies for the domain
2. Try logging in again

## If Still Failing

### Option A: Use Database Sessions
```bash
php artisan session:table
php artisan migrate
```

Update `.env`:
```env
SESSION_DRIVER=database
```

### Option B: Check Web Server Configuration
- Ensure PHP sessions are enabled
- Check PHP session.save_path is writable
- Verify cookie domain matches your site domain

## Files Updated
- `app/Http/Controllers/AuthController.php` - Added session initialization in showLoginForm() and login()
