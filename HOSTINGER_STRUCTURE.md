# Hostinger Laravel Folder Structure Guide

## 📁 Correct Folder Structure

Based on your screenshot, here's the correct structure for Hostinger:

### Structure Option 1: Standard Laravel (Recommended)

```
/home/your-username/domains/your-domain.com/
│
├── public_html/                    ← This is your web root
│   ├── index.php                   ← Laravel entry point
│   ├── .htaccess                   ← Apache rewrite rules
│   ├── storage/                    ← SYMLINK (not a real folder!)
│   │   └── images/                 ← Points to ../storage/app/public/images/
│   ├── assets/                     ← Your CSS, JS if any
│   └── robots.txt
│
├── app/                            ← Laravel application files
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/                        ← REAL storage folder
│   ├── app/
│   │   └── public/
│   │       └── images/             ← ACTUAL image files stored here
│   ├── framework/
│   └── logs/
├── vendor/
├── .env                            ← Environment configuration
├── artisan
└── composer.json
```

### Structure Option 2: If Everything is in public_html

If your hosting requires everything in `public_html`:

```
public_html/
├── index.php
├── .htaccess
├── storage/                        ← SYMLINK
│   └── images/
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage_real/                   ← Renamed to avoid conflict
│   └── app/
│       └── public/
│           └── images/
├── vendor/
└── .env
```

## 🔍 Current Issue in Your Setup

From your file manager, I see:
- ✅ `storage` folder exists in `public_html`
- ⚠️ It has permissions `drwxrwxrwx` (777) - this suggests it might be a real folder, not a symlink
- ⚠️ It was modified "2 minutes ago" - suggests it's being used

## ✅ Quick Fix Steps

### Step 1: Check if `storage` is a Symlink or Real Folder

**In Hostinger File Manager:**
1. Right-click on the `storage` folder
2. Check "Properties" or "Info"
3. If it shows "Type: Symbolic Link" → Good! ✅
4. If it shows "Type: Directory" → Need to fix! ❌

### Step 2: If It's a Real Folder (Not Symlink)

**Option A: Via Hostinger Terminal/SSH**

1. Access Hostinger Terminal (in hPanel)
2. Navigate to your domain:
   ```bash
   cd domains/your-domain.com/public_html
   ```
3. Check if it's a symlink:
   ```bash
   ls -la | grep storage
   ```
4. If it shows `storage -> ../storage/app/public` → It's a symlink ✅
5. If it shows `drwxrwxrwx storage` → It's a real folder ❌

6. **If real folder, delete and recreate:**
   ```bash
   rm -rf storage
   php artisan storage:link
   ```

**Option B: Via File Manager**

1. **Backup first!** Download any images from `public_html/storage/images/` if they exist
2. Delete the `storage` folder in `public_html`
3. Use Hostinger Terminal to run:
   ```bash
   cd public_html
   php artisan storage:link
   ```

### Step 3: Verify the Symlink

After creating the symlink:
- In file manager, `storage` should show a link icon or different appearance
- Clicking it should navigate to `storage/app/public`
- Files uploaded should appear in `storage/app/public/images/`

## 📋 Complete Hostinger Setup Checklist

### 1. File Structure
- [ ] Laravel files are in the correct location
- [ ] `public_html` contains only `index.php`, `.htaccess`, and symlinks
- [ ] `storage` in `public_html` is a symlink, not a real folder

### 2. Configuration Files
- [ ] `.env` file exists and is configured
- [ ] `APP_URL` in `.env` matches your domain
- [ ] Database credentials are correct

### 3. Permissions
- [ ] `storage/` folder: 755
- [ ] `bootstrap/cache/`: 755
- [ ] `storage/app/public/`: 755
- [ ] Uploaded images: 644

### 4. Symlink
- [ ] `public_html/storage` → points to `../storage/app/public`
- [ ] Test: Upload an image and verify it's accessible

## 🛠️ Hostinger-Specific Commands

### Access Terminal in Hostinger

1. Login to hPanel
2. Go to "Advanced" → "Terminal" or "SSH Access"
3. Navigate to your domain directory

### Common Commands

```bash
# Navigate to your domain
cd domains/your-domain.com/public_html

# Check Laravel version
php artisan --version

# Create storage symlink
php artisan storage:link

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set permissions (if needed)
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Check if symlink exists
ls -la | grep storage
```

## 🔧 If Symlink Creation Fails

If `php artisan storage:link` doesn't work:

1. **Check if symlinks are allowed:**
   - Some shared hosting blocks symlinks
   - Contact Hostinger support to enable symlinks

2. **Use the fallback route (Already implemented):**
   - The application automatically uses route-based URLs if symlink doesn't exist
   - Images will work via `/storage/{path}` route
   - No action needed - it works automatically!

3. **Manual workaround:**
   - Keep images in `public_html/storage/images/` (not recommended)
   - Update `config/filesystems.php` to use `public` disk differently

## 📝 Important Notes for Hostinger

1. **`public_html` is your web root** - Only files here are publicly accessible
2. **Symlinks are preferred** - Better security and organization
3. **File permissions matter** - 755 for folders, 644 for files
4. **`.env` file** - Must be outside `public_html` for security
5. **Composer** - Run `composer install --no-dev` for production

## 🧪 Testing Your Setup

1. **Upload a test image:**
   - Go to admin panel → Places → Create
   - Upload an image
   - Check if it saves

2. **Verify file location:**
   - Check `storage/app/public/images/` for the uploaded file
   - File should appear there, NOT in `public_html/storage/`

3. **Test image URL:**
   - Visit: `https://yourdomain.com/storage/images/filename.jpg`
   - Should display the image

4. **Check logs:**
   - View `storage/logs/laravel.log` for any errors

## 🆘 Troubleshooting

### Problem: "Storage folder is a real directory, not symlink"
**Solution:** Delete it and run `php artisan storage:link`

### Problem: "Can't create symlink - permission denied"
**Solution:** 
- Check folder permissions
- Contact Hostinger support
- Use the fallback route (already works!)

### Problem: "Images upload but don't display"
**Solution:**
- Check file permissions (should be 644)
- Verify symlink exists
- Check browser console for 404 errors
- The fallback route should handle this automatically

### Problem: "500 Internal Server Error"
**Solution:**
- Check `storage/logs/laravel.log`
- Verify `.env` file exists
- Check file permissions
- Clear cache: `php artisan cache:clear`

## 📞 Need More Help?

1. Check `storage/logs/laravel.log` for detailed errors
2. Verify all files are uploaded correctly
3. Test with a simple image upload
4. Contact Hostinger support if symlink creation is blocked

---

**Remember:** The application now has automatic fallback, so images will work even without the symlink. However, creating the symlink is still recommended for better performance and standard Laravel practices.

