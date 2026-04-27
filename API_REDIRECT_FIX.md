# API Redirect to Login Page - FIXED

## Problem
When accessing API endpoints like `https://webcms.tourliz.com/api/v1/bookings` without authentication, the request was redirecting to the login page (HTML) instead of returning a JSON 401 error response.

## Root Cause
Laravel's `Authenticate` middleware was checking `$request->expectsJson()`, but API requests without the `Accept: application/json` header were being treated as web requests and redirected to the login page.

## Solution Applied

### 1. Updated Authenticate Middleware
**File:** `app/Http/Middleware/Authenticate.php`

Modified to check if the request is for an API route (`/api/*`) and return `null` instead of redirecting, which causes Laravel to return a JSON 401 response.

```php
protected function redirectTo($request)
{
    // API routes should always return JSON, never redirect
    if ($request->is('api/*') || $request->expectsJson()) {
        return null;
    }
    
    return route('login');
}
```

### 2. Created ForceJsonResponse Middleware
**File:** `app/Http/Middleware/ForceJsonResponse.php`

New middleware that forces all API routes to accept JSON responses by setting the `Accept` header.

### 3. Added Middleware to API Group
**File:** `app/Http/Kernel.php`

Added `ForceJsonResponse` middleware to the `api` middleware group so it runs for all API requests.

### 4. Updated Exception Handler
**File:** `app/Exceptions/Handler.php`

Updated the `render()` method to force JSON responses for API routes, ensuring all exceptions return JSON instead of HTML.

## Testing

### Test 1: Unauthenticated API Request
```bash
curl -X GET https://webcms.tourliz.com/api/v1/bookings
```

**Expected Response (401):**
```json
{
    "message": "Unauthenticated."
}
```

**Before Fix:** Would redirect to HTML login page  
**After Fix:** Returns JSON 401 error

### Test 2: Authenticated API Request
```bash
curl -X GET https://webcms.tourliz.com/api/v1/bookings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Expected Response (200):**
```json
{
    "data": [...],
    "meta": {...}
}
```

### Test 3: Public API Endpoint
```bash
curl -X GET https://webcms.tourliz.com/api/test/public
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Public endpoint is accessible",
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

## Files Modified

1. ✅ `app/Http/Middleware/Authenticate.php` - Updated redirect logic
2. ✅ `app/Http/Middleware/ForceJsonResponse.php` - New middleware (created)
3. ✅ `app/Http/Kernel.php` - Added middleware to API group
4. ✅ `app/Exceptions/Handler.php` - Force JSON for API routes

## Additional Notes

- The API uses `Admin` model for authentication (not `User` model)
- Bookings are associated with `admin_id` field, which is correct
- All API routes now automatically return JSON responses
- No need to include `Accept: application/json` header (but it's still recommended)

## Verification Steps

1. **Test unauthenticated request:**
   ```
   GET https://webcms.tourliz.com/api/v1/bookings
   ```
   Should return: `{"message": "Unauthenticated."}` (401)

2. **Test with authentication:**
   - First login: `POST /api/auth/login`
   - Use token: `GET /api/v1/bookings` with `Authorization: Bearer {token}`
   - Should return booking data (200)

3. **Test public endpoint:**
   ```
   GET https://webcms.tourliz.com/api/test/public
   ```
   Should return success message (200)

## Deployment

After deploying these changes:

1. Clear all caches:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

2. Test the endpoints to verify they return JSON instead of redirecting

3. Update any client applications to handle JSON 401 responses instead of HTML redirects

