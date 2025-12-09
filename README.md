# Tourliz CMS

A Laravel-based Content Management System for Tourliz website, managing Places, Packages, and Services.

## Features

- **Places Management**: Manage tourist places with location, images, and details
- **Packages Management**: Create and manage tour packages with pricing and itineraries
- **Services Management**: Manage tourism services
- **RESTful API**: Clean JSON API for frontend integration
- **Admin Panel**: Full CRUD operations for content management

## Database Setup

1. Create a MySQL database named `tourliz_site`
2. Update `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tourliz_site
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```

## API Endpoints

### Places API
- `GET /api/v1/places` - List all active places (with filters: category, city, country, featured, search)
- `GET /api/v1/places/{slug}` - Get a specific place by slug

### Packages API
- `GET /api/v1/packages` - List all active packages (with filters: min_price, max_price, duration_days, featured, search)
- `GET /api/v1/packages/{slug}` - Get a specific package by slug

### Services API
- `GET /api/v1/services` - List all active services (with filters: category, min_price, max_price, featured, search)
- `GET /api/v1/services/{slug}` - Get a specific service by slug

### API Query Parameters
- `per_page` - Number of items per page (default: 15)
- `sort_by` - Field to sort by (default: sort_order)
- `sort_order` - Sort direction: asc or desc (default: asc)
- `search` - Search by name
- `featured` - Filter featured items (true/false)
- Model-specific filters (see above)

## Admin CMS Endpoints

All admin endpoints return JSON responses and support full CRUD operations:

### Places Management
- `GET /admin/places` - List all places
- `POST /admin/places` - Create a new place
- `GET /admin/places/{id}` - Get a specific place
- `PUT/PATCH /admin/places/{id}` - Update a place
- `DELETE /admin/places/{id}` - Delete a place

### Packages Management
- `GET /admin/packages` - List all packages
- `POST /admin/packages` - Create a new package
- `GET /admin/packages/{id}` - Get a specific package
- `PUT/PATCH /admin/packages/{id}` - Update a package
- `DELETE /admin/packages/{id}` - Delete a package

### Services Management
- `GET /admin/services` - List all services
- `POST /admin/services` - Create a new service
- `GET /admin/services/{id}` - Get a specific service
- `PUT/PATCH /admin/services/{id}` - Update a service
- `DELETE /admin/services/{id}` - Delete a service

## Storage

The storage link has been created. Upload images to `storage/app/public` and they will be accessible via the public URL.

## License

This project is proprietary software for Tourliz.
