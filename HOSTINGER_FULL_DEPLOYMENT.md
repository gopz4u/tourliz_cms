# Hostinger Full Project Deployment Guide — Tourliz CMS

This guide covers how to deploy the entire **Tourliz CMS** application to your Hostinger Web Hosting server (`webcms.tourliz.com`).

---

## 1. Project Deployment Package (`tourliz_cms_full_hostinger.zip`)

The archive [`tourliz_cms_full_hostinger.zip`](file:///c:/xampp/htdocs/tourliz_cms/tourliz_cms_full_hostinger.zip) contains the complete production-ready application including:
- All core application logic (`app/`, `config/`, `database/`, `resources/`, `routes/`)
- All vendor dependencies (`vendor/`)
- Public front-controller assets (`public/`)
- Full Database migrations & unified B2B/B2C itinerary architecture
- Automated deployment script `deploy_hostinger.sh`

---

## 2. Server Directory Setup on Hostinger

On Hostinger (hPanel), your domain `webcms.tourliz.com` will have a root folder:
```
/home/u123456789/domains/webcms.tourliz.com/
```

### Option A: App Root in Domain Directory (Recommended Security Setup)
```
/home/username/domains/webcms.tourliz.com/
├── app/
├── bootstrap/
├── config/
├── database/
├── public_html/             <-- Web root (only public files)
│   ├── index.php
│   ├── .htaccess
│   └── storage/             <-- Symlink to ../storage/app/public
├── resources/
├── routes/
├── storage/
│   ├── app/public/
│   ├── framework/
│   └── logs/
├── vendor/
├── .env
└── deploy_hostinger.sh
```

---

## 3. Deployment Steps

### Step 1: Upload Archive to Hostinger
1. Log in to **Hostinger hPanel** -> **Files** -> **File Manager**.
2. Navigate to `/home/username/domains/webcms.tourliz.com/`.
3. Upload [`tourliz_cms_full_hostinger.zip`](file:///c:/xampp/htdocs/tourliz_cms/tourliz_cms_full_hostinger.zip).

### Step 2: Extract Archive
- Right-click `tourliz_cms_full_hostinger.zip` and click **Extract**.
- Extract into the domain root directory.

### Step 3: Configure Hostinger Production `.env`
Create or edit `.env` in the domain root directory with production parameters:
```env
APP_NAME="Tourliz CMS"
APP_ENV=production
APP_KEY=base64:KW1Ojr8ctmZCUaek9o1v2m/I0ZNMfvYPHq59A/XZt00=
APP_DEBUG=false
APP_URL=https://webcms.tourliz.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_hostinger_dbname
DB_USERNAME=your_hostinger_dbuser
DB_PASSWORD=your_hostinger_dbpassword

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_DOMAIN=.tourliz.com
SESSION_SECURE_COOKIE=true
CORS_ALLOWED_ORIGINS=https://tourliz.com,https://webcms.tourliz.com

AWS_ACCESS_KEY_ID=e393595f56b7f0d6a4af889017b6516e
AWS_SECRET_ACCESS_KEY=cbcecb10132f25e2b735048ef5e146a8434345ff7bb72967c463ae301e5ccea9
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=tourlizimg
AWS_ENDPOINT=https://e5f6c5fb945c78c20decc5d1e1149486.r2.cloudflarestorage.com
AWS_URL=https://img.tourliz.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

### Step 4: Execute Automated Server Commands
Open Hostinger SSH Terminal or Terminal feature and execute:
```bash
cd /home/username/domains/webcms.tourliz.com/
bash deploy_hostinger.sh
```

---

## 4. Verification Checklist

1. **Database Migrations**: `php artisan migrate --force` creates and migrates unified `itineraries`, `itinerary_days`, `itinerary_day_items`, `itinerary_b2b_details`, and `itinerary_b2c_details`.
2. **Storage Symlink**: `php artisan storage:link` links `public_html/storage` to `storage/app/public`.
3. **CORS & Cookies**: Sanctum sessions share cookies seamlessly between `webcms.tourliz.com` and `tourliz.com`.
4. **Cache Optimization**: Configuration, routes, and views compiled cleanly (`php artisan config:cache`, `php artisan route:cache`).
