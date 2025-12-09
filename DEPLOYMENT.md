# Deployment Guide - Image Storage Fix

## Issue
Images are not displaying on the hosting server because the symbolic link from `public/storage` to `storage/app/public` doesn't exist.

## Solution

The application now includes a fallback mechanism that works even without the symlink. However, for best performance, you should create the symlink.

### Option 1: Create Symbolic Link (Recommended)

Run this command on your server via SSH:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

### Option 2: Automatic Fallback (Already Implemented)

If the symlink doesn't exist, the application will automatically use a route-based URL to serve images. This is already implemented and will work automatically.

### Option 3: Manual Symlink Creation

If `php artisan storage:link` doesn't work on your hosting server, you can create the symlink manually:

**Via SSH:**
```bash
cd /path/to/your/project/public
ln -s ../storage/app/public storage
```

**Via cPanel File Manager:**
1. Navigate to `public` folder
2. Create a new symbolic link
3. Link name: `storage`
4. Target: `../storage/app/public`

### Verify the Fix

1. Upload an image through the admin panel
2. Check if the image displays correctly
3. If images still don't display, check:
   - File permissions on `storage/app/public` (should be 755)
   - File permissions on uploaded images (should be 644)
   - `.htaccess` file exists in `public` folder

### File Permissions

Ensure proper permissions:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 644 storage/app/public/images/*
```

### After Deployment

Run these commands:
```bash
composer dump-autoload
php artisan config:cache
php artisan route:cache
```

## Troubleshooting

### Images still not showing?

1. **Check if storage directory exists:**
   ```bash
   ls -la storage/app/public
   ```

2. **Check if symlink exists:**
   ```bash
   ls -la public/storage
   ```

3. **Check file permissions:**
   ```bash
   ls -la storage/app/public/images/
   ```

4. **Check web server configuration:**
   - Ensure mod_rewrite is enabled (Apache)
   - Check nginx configuration allows access to storage

5. **Test the route directly:**
   Visit: `https://yourdomain.com/storage/images/filename.jpg`
   If this works, the route-based fallback is working.

### Still having issues?

The application now uses a helper function `getImageUrl()` that automatically:
- Checks if symlink exists
- Falls back to route-based URLs if symlink doesn't exist
- Handles full URLs correctly

All Resource classes and the UploadController have been updated to use this helper.

