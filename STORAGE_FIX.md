# Storage 404 Error Fix Guide

## Problem
URLs like `https://webcms.tourliz.com/storage/images/1765304789_3GWdmy3yhV.jpeg` return 404 error.

## Root Cause
The route `/storage/{path}` exists but might not be matching due to:
1. `.htaccess` redirecting to `index.php` before route matching
2. File doesn't exist at expected location
3. Route not being prioritized correctly

## Solutions

### Solution 1: Create Storage Symlink (Recommended)

**Via SSH/Terminal:**
```bash
cd /path/to/your/laravel/public
php artisan storage:link
```

**Verify symlink:**
```bash
ls -la public/storage
# Should show: storage -> ../storage/app/public
```

### Solution 2: Check File Location

Verify the file actually exists:
```bash
ls -la storage/app/public/images/1765304789_3GWdmy3yhV.jpeg
```

If file doesn't exist, check:
```bash
find storage/app/public -name "*.jpeg" -type f
```

### Solution 3: Update .htaccess (If Needed)

Make sure `.htaccess` in `public` folder allows the route to work:

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

### Solution 4: Check Permissions

```bash
# Storage directory
chmod -R 755 storage
chmod -R 755 storage/app/public
chmod -R 644 storage/app/public/images/*

# Public storage (if symlink)
chmod -R 755 public/storage
```

### Solution 5: Test Route Directly

Test if the route is working:
1. Visit: `https://webcms.tourliz.com/storage/images/1765304789_3GWdmy3yhV.jpeg`
2. Check `storage/logs/laravel.log` for errors
3. Look for "StorageController: Attempting to serve image" log entry

### Solution 6: Verify APP_URL

Check `.env` file:
```env
APP_URL=https://webcms.tourliz.com
```

Then clear config cache:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## Debugging Steps

1. **Check if file exists:**
   ```bash
   ls -la storage/app/public/images/1765304789_3GWdmy3yhV.jpeg
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Then try accessing the image URL and watch for log entries.

3. **Test route manually:**
   ```bash
   php artisan route:list | grep storage
   ```

4. **Check if symlink exists:**
   ```bash
   ls -la public/storage
   ```

5. **Test file access:**
   ```bash
   php -r "echo file_exists('storage/app/public/images/1765304789_3GWdmy3yhV.jpeg') ? 'EXISTS' : 'NOT FOUND';"
   ```

## Quick Fix Commands

Run these in order:

```bash
# 1. Navigate to Laravel root
cd /path/to/laravel

# 2. Create symlink
cd public
php artisan storage:link
cd ..

# 3. Set permissions
chmod -R 755 storage
chmod -R 755 storage/app/public
find storage/app/public/images -type f -exec chmod 644 {} \;

# 4. Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 5. Test
curl -I https://webcms.tourliz.com/storage/images/1765304789_3GWdmy3yhV.jpeg
```

## Expected Behavior

After fixing:
- ✅ `https://webcms.tourliz.com/storage/images/1765304789_3GWdmy3yhV.jpeg` should return 200 OK
- ✅ Image should display in browser
- ✅ No 404 errors in logs

## If Still Not Working

1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify web server (Apache/Nginx) configuration
3. Check if mod_rewrite is enabled (Apache)
4. Verify file actually exists at the path
5. Check if there are any middleware blocking the route

