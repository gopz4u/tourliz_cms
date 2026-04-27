# Shared Hosting Image Display Fix

## Problem
- ✅ Works on localhost: `http://127.0.0.1:8000/storage/images/1765214902_U5VwWXQ23d.png`
- ❌ Not working on Hostinger: `https://webcms.tourliz.com/storage/images/1765315800_qMWe0ZecOx.png`

## Root Causes on Shared Hosting

1. **Path Resolution Differences**: Shared hosting may resolve paths differently
2. **Symlink Issues**: Symlinks might not work or be blocked
3. **Route Matching**: Routes might not match due to .htaccess or server config
4. **File Permissions**: Files might exist but not be readable

## Solution Implemented

### 1. Enhanced StorageController
- Now checks multiple possible file locations
- Handles different path resolutions on shared hosting
- Better logging for debugging

### 2. Route Priority
- Storage route is at the top of routes file
- Ensures `/storage/*` is matched first

### 3. Multiple Path Detection
The controller now checks:
- `storage/app/public/` (standard)
- `base_path('storage/app/public/')` (alternative)
- `public_path('storage/')` (if symlink exists)
- Real path resolution (for shared hosting)

## Steps to Fix on Hostinger

### Step 1: Clear All Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### Step 2: Verify File Exists
Check if the file actually exists on the server:
```bash
ls -la storage/app/public/images/1765315800_qMWe0ZecOx.png
```

### Step 3: Check Permissions
```bash
chmod -R 755 storage
chmod -R 755 storage/app/public
chmod -R 644 storage/app/public/images/*
```

### Step 4: Test Route
Visit the diagnostic route:
```
https://webcms.tourliz.com/storage-debug
```

This will show:
- Where files are expected
- If files exist
- Path information

### Step 5: Check Logs
```bash
tail -f storage/logs/laravel.log
```

Then try accessing the image URL and watch for log entries.

### Step 6: Create Symlink (If Possible)
```bash
cd public
php artisan storage:link
```

If this fails, the route-based fallback will work.

## Testing

1. **Test the route directly:**
   ```
   https://webcms.tourliz.com/storage/images/1765315800_qMWe0ZecOx.png
   ```

2. **Check diagnostic route:**
   ```
   https://webcms.tourliz.com/storage-debug
   ```

3. **Check Laravel logs:**
   - Look for "StorageController: Attempting to serve image"
   - Check which paths were tested
   - See if file was found

## Common Issues & Solutions

### Issue: Route not matching
**Solution:**
- Clear route cache: `php artisan route:clear`
- Check `.htaccess` is correct
- Verify mod_rewrite is enabled

### Issue: File not found
**Solution:**
- Verify file exists: `ls -la storage/app/public/images/`
- Check file permissions
- Check if path is correct on server

### Issue: 500 Error
**Solution:**
- Check `storage/logs/laravel.log`
- Verify file permissions
- Check PHP error logs

### Issue: Permission Denied
**Solution:**
```bash
chmod -R 755 storage
chmod -R 755 storage/app/public
chmod -R 644 storage/app/public/images/*
```

## What Changed

1. **StorageController** now:
   - Checks multiple path locations
   - Uses realpath() for better resolution
   - Handles shared hosting path differences
   - Better error logging

2. **Route priority**:
   - Storage route is first in routes file
   - Ensures it's matched before other routes

3. **Path detection**:
   - Tries standard Laravel paths
   - Tries alternative paths for shared hosting
   - Uses realpath() for absolute paths

## Expected Behavior

After fix:
- ✅ Route matches `/storage/*` requests
- ✅ Controller finds file in one of the checked locations
- ✅ Image is served with correct MIME type
- ✅ No 404 errors

## Debugging Commands

```bash
# Check if file exists
php -r "echo file_exists('storage/app/public/images/1765315800_qMWe0ZecOx.png') ? 'EXISTS' : 'NOT FOUND';"

# Check storage path
php artisan tinker
>>> storage_path('app/public')
>>> base_path()
>>> public_path()

# List route
php artisan route:list | grep storage

# Check logs
tail -20 storage/logs/laravel.log
```

## If Still Not Working

1. Check `storage/logs/laravel.log` for detailed errors
2. Visit `/storage-debug` route for diagnostic info
3. Verify file exists at: `storage/app/public/images/1765315800_qMWe0ZecOx.png`
4. Check file permissions
5. Verify `.env` has correct `APP_URL`
6. Contact Hostinger support if symlinks are blocked

