# Hostinger Laravel Setup Guide

## Correct Folder Structure for Hostinger

On Hostinger, your Laravel application should be structured as follows:

```
public_html/
├── index.php                    (Laravel's public/index.php)
├── .htaccess                    (Laravel's public/.htaccess)
├── storage/                      (SYMLINK to ../storage/app/public)
│   └── images/                  (Should be a symlink, not a real folder)
├── assets/                      (CSS, JS, etc. if any)
└── ...                          (Other public files)

(One level up from public_html)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
│   ├── app/
│   │   └── public/
│   │       └── images/          (ACTUAL image storage location)
│   ├── framework/
│   └── logs/
├── vendor/
├── .env
├── artisan
└── composer.json
```

## Current Issue

From your file manager screenshot, I can see:
- `storage` folder exists in `public_html` with permissions `drwxrwxrwx`
- This should be a **symlink**, not a regular folder

## Solution Steps

### Option 1: Create Proper Symlink (Recommended)

1. **Delete the current `storage` folder in `public_html`** (if it's a real folder, not a symlink)

2. **Via SSH (if available):**
   ```bash
   cd /home/your-username/domains/your-domain.com/public_html
   rm -rf storage
   php artisan storage:link
   ```

3. **Via File Manager:**
   - Delete the `storage` folder in `public_html`
   - Use Hostinger's terminal/SSH feature to run: `php artisan storage:link`

### Option 2: Manual Symlink Creation (If SSH Available)

```bash
cd /path/to/public_html
ln -s ../storage/app/public storage
```

### Option 3: Use the Fallback Route (Already Implemented)

The application already has a fallback route that will serve images even without the symlink. However, creating the symlink is still recommended for better performance.

## Hostinger-Specific Setup

### 1. Application Root Location

On Hostinger, you typically have two options:

**Option A: Application in `public_html` (Not Recommended)**
```
public_html/
├── app/
├── public/
│   ├── index.php
│   └── .htaccess
└── ...
```
- Requires modifying `.htaccess` to point to `public/` folder
- Less secure

**Option B: Application Outside `public_html` (Recommended)**
```
/home/username/domains/domain.com/
├── public_html/          (Only public files)
│   ├── index.php
│   ├── .htaccess
│   └── storage/          (symlink)
└── (other Laravel files outside public_html)
```

### 2. Update `.htaccess` in `public_html`

If your Laravel files are in `public_html` directly, you need to update the `.htaccess`:

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

### 3. Update `index.php` Paths

If your Laravel root is one level up from `public_html`, update `public_html/index.php`:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Adjust this path if your Laravel root is in a different location
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

## File Permissions

Set correct permissions:

```bash
# Storage directory
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Public storage (if symlink doesn't work, create manually)
chmod -R 755 storage/app/public
```

## Verify Setup

1. **Check if symlink exists:**
   - In file manager, `storage` should show as a link/symlink icon
   - Clicking it should navigate to `storage/app/public`

2. **Test image upload:**
   - Upload an image through admin panel
   - Check if it appears in `storage/app/public/images/`
   - Check if it's accessible via `https://yourdomain.com/storage/images/filename.jpg`

3. **Check application logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Common Hostinger Issues

### Issue 1: Can't Create Symlink
**Solution:** Use the fallback route (already implemented) or contact Hostinger support to enable symlink creation.

### Issue 2: 500 Error
**Solution:** 
- Check file permissions
- Check `.env` file exists and is configured
- Check `storage/logs/laravel.log` for errors

### Issue 3: Images Not Displaying
**Solution:**
- The application now has automatic fallback
- Images will work via `/storage/{path}` route if symlink doesn't exist
- Still recommended to create symlink for better performance

## Quick Fix for Your Current Setup

Since you have a `storage` folder in `public_html`:

1. **Check if it's a symlink or real folder:**
   - In file manager, check the folder properties
   - If it's a real folder, you need to delete it and create a symlink

2. **If it's a real folder with images:**
   - Move images from `public_html/storage/images/` to `storage/app/public/images/`
   - Delete `public_html/storage` folder
   - Create symlink: `php artisan storage:link`

3. **Test:**
   - Upload a new image
   - Verify it appears in `storage/app/public/images/`
   - Check if accessible via browser

## Need Help?

If you're still having issues:
1. Check `storage/logs/laravel.log` for errors
2. Verify `.env` file has correct paths
3. Ensure `APP_URL` in `.env` matches your domain
4. Contact Hostinger support if symlink creation is blocked

