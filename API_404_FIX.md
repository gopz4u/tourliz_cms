# API 404 Error Fix Guide

## Problem
All API endpoints at `https://webcms.tourliz.com/api/*` are returning 404 errors.

## Common Causes on Shared Hosting

### 1. Document Root Not Pointing to `public` Directory (MOST COMMON)

**Issue:** If your document root is pointing to the project root instead of the `public` folder, Laravel won't work.

**Solution:**

#### Option A: Change Document Root in Hosting Panel
1. Log into your hosting control panel (cPanel/Hostinger)
2. Go to **Domain Settings** or **Document Root**
3. Change document root from:
   ```
   /home/username/domains/webcms.tourliz.com/public_html
   ```
   To:
   ```
   /home/username/domains/webcms.tourliz.com/public_html/public
   ```
   OR if Laravel is outside public_html:
   ```
   /home/username/domains/webcms.tourliz.com/public_html
   ```
   (where `public_html` contains only the contents of Laravel's `public` folder)

#### Option B: Move Files (If you can't change document root)
1. Move all contents of `public/` folder to `public_html/`
2. Update `public_html/index.php` paths:
   ```php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   Adjust paths based on where your Laravel root is located.

### 2. `.htaccess` File Missing or Not Working

**Check:**
1. Verify `.htaccess` exists in `public/` (or `public_html/`)
2. Ensure mod_rewrite is enabled on your server

**Fix `.htaccess`:**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 3. Route Cache Issues

**Clear all caches:**
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

**DO NOT run these on production (they can cause issues):**
```bash
# DON'T RUN THESE:
php artisan config:cache
php artisan route:cache
```

### 4. PHP Version Issues

**Check PHP version:**
```bash
php -v
```

Laravel 9+ requires PHP 8.0+. If you're on PHP 7.x, upgrade to PHP 8.0+.

### 5. File Permissions

**Set correct permissions:**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 public/.htaccess
```

## Step-by-Step Troubleshooting

### Step 1: Test if Laravel is Working

Visit these URLs to test:

1. **Root URL:**
   ```
   https://webcms.tourliz.com/
   ```
   Should show the landing page.

2. **API Test Endpoint:**
   ```
   https://webcms.tourliz.com/api/test/public
   ```
   Should return JSON: `{"success":true,"message":"Public endpoint is accessible",...}`

3. **Direct index.php test:**
   ```
   https://webcms.tourliz.com/index.php/api/test/public
   ```
   If this works but the clean URL doesn't, it's an `.htaccess` issue.

### Step 2: Check Server Configuration

**Via SSH, check:**
```bash
# Check if .htaccess exists
ls -la public/.htaccess

# Check if mod_rewrite is enabled
php -i | grep mod_rewrite

# Check document root
echo $_SERVER['DOCUMENT_ROOT']
```

**Via PHP file (create `test.php` in public_html):**
```php
<?php
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
phpinfo();
?>
```

### Step 3: Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Then try accessing an API endpoint and watch for errors.

### Step 4: Verify Routes are Loaded

**Create a test route in `routes/web.php`:**
```php
Route::get('/api-test', function() {
    return response()->json([
        'routes_loaded' => true,
        'timestamp' => now()
    ]);
});
```

Visit: `https://webcms.tourliz.com/api-test`

If this works, routes are loading but API routes specifically aren't.

## Quick Fixes to Try

### Fix 1: Clear All Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### Fix 2: Verify `.htaccess` is in Correct Location
- If document root is `public_html/`, `.htaccess` should be in `public_html/`
- If document root is `public_html/public/`, `.htaccess` should be in `public_html/public/`

### Fix 3: Check `index.php` Paths

If your Laravel root is one level up from `public_html`, update `public_html/index.php`:

```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

If Laravel root is two levels up:
```php
require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
```

### Fix 4: Test with Direct index.php

Try accessing:
```
https://webcms.tourliz.com/index.php/api/test/public
```

If this works, the issue is `.htaccess` not rewriting URLs.

## Hostinger-Specific Fixes

### Option 1: Proper Folder Structure (Recommended)

```
/home/username/domains/webcms.tourliz.com/
├── public_html/              (Document root - contains only public files)
│   ├── index.php
│   ├── .htaccess
│   └── storage/          (symlink to ../storage/app/public)
└── (Laravel root - one level up)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── routes/
    ├── storage/
    └── vendor/
```

**Then update `public_html/index.php`:**
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

### Option 2: All Files in public_html (Less Secure)

Move all Laravel files into `public_html/` and update paths accordingly.

## Verification

After applying fixes, test these endpoints:

1. **Public Test:**
   ```
   GET https://webcms.tourliz.com/api/test/public
   ```
   Expected: `{"success":true,"message":"Public endpoint is accessible"}`

2. **Auth Login:**
   ```
   POST https://webcms.tourliz.com/api/auth/login
   Content-Type: application/json
   {"email":"test@example.com","password":"password"}
   ```

3. **Packages List:**
   ```
   GET https://webcms.tourliz.com/api/v1/packages
   ```

## Still Not Working?

1. **Check error logs:**
   - `storage/logs/laravel.log`
   - Server error logs (via hosting panel)

2. **Contact hosting support:**
   - Ask them to verify document root is correct
   - Ask if mod_rewrite is enabled
   - Ask about PHP version

3. **Enable debug mode temporarily:**
   In `.env`, set:
   ```
   APP_DEBUG=true
   ```
   This will show detailed error messages (disable after fixing).

## Common Error Messages

### "404 Not Found"
- Document root issue
- `.htaccess` not working
- Routes not loaded

### "500 Internal Server Error"
- Check `storage/logs/laravel.log`
- Check file permissions
- Check PHP version

### "Route [api.test.public] not defined"
- Routes not loaded
- Clear route cache: `php artisan route:clear`

## Prevention

1. **Never cache routes/config on shared hosting:**
   ```bash
   # DON'T DO THIS:
   php artisan route:cache
   php artisan config:cache
   ```

2. **Always clear caches after deployment:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

3. **Keep `.htaccess` in correct location**

4. **Verify document root points to `public/` folder**

