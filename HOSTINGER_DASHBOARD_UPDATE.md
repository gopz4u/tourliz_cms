# Tourliz Advisor Dashboard — Hostinger Update

This package updates the existing Tourliz Laravel application with the new travel-advisor dashboard. It does not change the database schema and does not require `npm` or Composer installation.

## Included files

- `app/Http/Controllers/Admin/DashboardController.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/layouts/admin.blade.php`

## Install on Hostinger

1. Back up the current application files and database.
2. Upload `tourliz_dashboard_hostinger_update.zip` to the Laravel application root (the folder containing `artisan`, not `public_html`).
3. Extract the archive and allow it to replace the three included application files.
4. From Hostinger Terminal, run:

```bash
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan view:cache
php artisan route:cache
```

5. Open `/admin/dashboard` and sign in with an admin account.

## Rollback

Restore the three files from the backup made in step 1, then run the same cache-clear commands.
