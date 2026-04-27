# Tourliz CMS - Server Deployment Guide

This guide will help you upload and deploy the Tourliz CMS to your server.

## Files to Upload to Server

### 1. Core Application Files
Upload all files and folders EXCEPT the following:

**DO NOT UPLOAD:**
- `.env` (create new one on server)
- `.git/` (if exists)
- `node_modules/` (if exists)
- `storage/logs/*` (keep folder, but not log files)
- `storage/framework/cache/*` (keep folder, but not cache files)
- `storage/framework/sessions/*` (keep folder, but not session files)
- `storage/framework/views/*` (keep folder, but not view files)

**MUST UPLOAD:**
```
/app
/bootstrap
/config
/database
/public
/resources
/routes
/storage (folders only, empty cache/logs)
/vendor (or install via composer on server)
/artisan
/composer.json
/composer.lock
/package.json (if exists)
/package-lock.json (if exists)
/.htaccess (if exists)
/README.md
/DEPLOYMENT_GUIDE.md
```

### 2. Upload Method

#### Option A: Using FTP/SFTP Client (FileZilla, WinSCP, etc.)
1. Connect to your server via FTP/SFTP
2. Navigate to your web root directory (usually `public_html` or `www`)
3. Upload all files maintaining the folder structure
4. Ensure file permissions are set correctly (see below)

#### Option B: Using Git (Recommended)
```bash
# On your local machine
git add .
git commit -m "Deploy to production"
git push origin main

# On server
cd /path/to/your/project
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Option C: Using ZIP Archive
1. Create a ZIP file of the project (excluding files mentioned above)
2. Upload ZIP to server
3. Extract in web root directory
4. Run setup commands (see below)

### 3. Server Setup Steps

#### Step 1: Upload Files
Upload all files to your server's web root directory.

#### Step 2: Set File Permissions
```bash
# Navigate to project directory
cd /path/to/your/project

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set storage and bootstrap/cache permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 3: Create .env File
```bash
# Copy .env.example to .env
cp .env.example .env

# Edit .env file with your server details
nano .env
```

Update these values in `.env`:
```env
APP_NAME="Tourliz CMS"
APP_ENV=production
APP_KEY=  # Run: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

#### Step 4: Install Dependencies
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate
```

#### Step 5: Run Migrations
```bash
# Run database migrations
php artisan migrate --force

# Seed sample data (optional)
php artisan db:seed --force
```

#### Step 6: Create Storage Link
```bash
# Create symbolic link for storage
php artisan storage:link
```

#### Step 7: Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 4. Web Server Configuration

#### Apache (.htaccess)
Ensure your `.htaccess` file in the `public` directory contains:
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

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Database Setup

#### Create Database
```sql
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'your_database_user'@'localhost' IDENTIFIED BY 'your_database_password';
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_database_user'@'localhost';
FLUSH PRIVILEGES;
```

### 6. Post-Deployment Checklist

- [ ] Files uploaded to server
- [ ] File permissions set correctly
- [ ] `.env` file created and configured
- [ ] Database created and credentials updated
- [ ] Dependencies installed (`composer install`)
- [ ] Application key generated
- [ ] Migrations run successfully
- [ ] Storage link created
- [ ] Application optimized (config, route, view cache)
- [ ] Web server configured correctly
- [ ] SSL certificate installed (for HTTPS)
- [ ] Test admin login
- [ ] Test API endpoints
- [ ] Test file uploads

### 7. Troubleshooting

#### Issue: 500 Internal Server Error
- Check file permissions
- Check `.env` file exists and is configured
- Check `storage` and `bootstrap/cache` are writable
- Check error logs: `storage/logs/laravel.log`

#### Issue: Database Connection Error
- Verify database credentials in `.env`
- Check database server is running
- Verify database user has proper permissions

#### Issue: Images Not Displaying
- Run `php artisan storage:link`
- Check `storage/app/public` permissions
- Verify `.htaccess` in public directory

#### Issue: Route Not Found
- Run `php artisan route:cache`
- Clear browser cache
- Check web server configuration

### 8. Security Recommendations

1. **Set APP_DEBUG=false** in production
2. **Use HTTPS** (SSL certificate)
3. **Restrict file permissions** (755 for directories, 644 for files)
4. **Keep Laravel updated** regularly
5. **Use strong database passwords**
6. **Enable firewall** on server
7. **Regular backups** of database and files
8. **Monitor error logs** regularly

### 9. Backup Strategy

#### Database Backup
```bash
# Create backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Restore backup
mysql -u username -p database_name < backup_20231223.sql
```

#### Files Backup
```bash
# Backup entire project
tar -czf tourliz_cms_backup_$(date +%Y%m%d).tar.gz /path/to/project

# Backup only storage
tar -czf storage_backup_$(date +%Y%m%d).tar.gz /path/to/project/storage
```

### 10. Maintenance Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check application status
php artisan about
```

## Quick Upload Checklist

1. ✅ Upload all files (except .env, node_modules, .git)
2. ✅ Set file permissions (755 for dirs, 644 for files)
3. ✅ Create .env file with server configuration
4. ✅ Run `composer install --no-dev`
5. ✅ Run `php artisan key:generate`
6. ✅ Run `php artisan migrate --force`
7. ✅ Run `php artisan storage:link`
8. ✅ Run optimization commands
9. ✅ Configure web server
10. ✅ Test the application

## Support

For issues or questions, check:
- Laravel Documentation: https://laravel.com/docs
- Server error logs: `storage/logs/laravel.log`
- Web server error logs

---

**Last Updated:** December 23, 2025

