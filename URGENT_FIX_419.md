# URGENT: Fix 419 Error - Step by Step

## The Problem
419 Page Expired error persists even after code updates. This means the **server configuration needs to be fixed**.

## CRITICAL: Update .env File on Server

**You MUST update your `.env` file on the server with these exact settings:**

```env
# Application
APP_NAME="Tourliz CMS"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://webcms.tourliz.com

# Session Configuration - CRITICAL FOR HTTPS
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none

# Cookie Configuration
COOKIE_DOMAIN=null
```

**IMPORTANT NOTES:**
- `SESSION_SECURE_COOKIE=true` - REQUIRED for HTTPS
- `SESSION_SAME_SITE=none` - REQUIRED for Chrome/HTTPS
- `SESSION_DOMAIN=null` - Don't set a domain

## Step-by-Step Server Fix

### Step 1: SSH into your server
```bash
ssh user@your-server
cd /path/to/tourliz_cms
```

### Step 2: Backup current .env
```bash
cp .env .env.backup
```

### Step 3: Edit .env file
```bash
nano .env
# or
vi .env
```

Add/update these lines:
```env
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none
SESSION_DOMAIN=null
```

### Step 4: Run these commands
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Clear old sessions
rm -rf storage/framework/sessions/*

# Fix permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Regenerate APP_KEY if needed
php artisan key:generate

# Rebuild config cache
php artisan config:cache
```

### Step 5: Verify
```bash
# Check if config is correct
php artisan tinker
>>> config('session.secure')
=> true
>>> config('session.same_site')
=> "none"
>>> exit
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

## Browser Testing

After server fix:
1. **Clear ALL cookies** for webcms.tourliz.com
2. **Use incognito/private mode**
3. Try logging in
4. Check browser console (F12) for errors

## Verify Cookies Are Being Set

Open browser DevTools (F12):
1. Go to **Application** tab (Chrome) or **Storage** tab (Firefox)
2. Click **Cookies** → `https://webcms.tourliz.com`
3. You should see:
   - `laravel_session` cookie
   - `XSRF-TOKEN` cookie
4. Check cookie properties:
   - **Secure**: Should be checked (for HTTPS)
   - **SameSite**: Should be "None" (for HTTPS)

## If Still Not Working

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Check Session Files
```bash
ls -la storage/framework/sessions/
# Should see session files being created
```

### Test Session Directly
Create a test route (temporary):
```php
// routes/web.php
Route::get('/test-session', function() {
    session(['test' => 'working']);
    return response()->json([
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'secure' => config('session.secure'),
        'same_site' => config('session.same_site'),
    ]);
});
```

Visit: `https://webcms.tourliz.com/test-session`

## Files Updated

1. `config/session.php` - Auto-detects HTTPS and sets secure/none
2. `app/Http/Middleware/VerifyCsrfToken.php` - Better error logging
3. `fix_419_server.sh` - Automated fix script

## Most Common Issue

**The .env file on the server is NOT updated!**

The code changes won't work if the `.env` file doesn't have:
- `SESSION_SECURE_COOKIE=true`
- `SESSION_SAME_SITE=none`

**You MUST update the .env file on the server and run `php artisan config:clear && php artisan config:cache`**
