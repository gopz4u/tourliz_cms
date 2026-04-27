# TourLiz CMS API Documentation

## Base URL
```
https://webcms.tourliz.com/api
```

## Authentication

The API uses Bearer Token authentication via Laravel Sanctum. You need to obtain a token by logging in first.

### Getting Your Bearer Token

**1. Login to get token:**
```bash
POST /api/auth/login
Content-Type: application/json

{
    "email": "your-email@example.com",
    "password": "your-password"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Your Name",
        "email": "your-email@example.com"
    },
    "token": "1|abc123xyz789token..."
}
```

### Using the Bearer Token

Once you have the token, include it in the `Authorization` header for all protected endpoints:

```bash
Authorization: Bearer 1|abc123xyz789token...
```

**Example using cURL:**
```bash
curl -X GET "https://webcms.tourliz.com/api/v1/packages" \
  -H "Authorization: Bearer 1|abc123xyz789token..." \
  -H "Accept: application/json"
```

**Example using JavaScript (Fetch):**
```javascript
fetch('https://webcms.tourliz.com/api/v1/packages', {
  headers: {
    'Authorization': 'Bearer 1|abc123xyz789token...',
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

**Example using Postman:**
1. Go to the **Authorization** tab
2. Select **Bearer Token** as the type
3. Paste your token in the Token field

---

## Test Endpoints

### Test Authentication (Public)
```http
GET /api/test/public
```

**Response:**
```json
{
    "success": true,
    "message": "Public endpoint is accessible",
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### Test Authentication (Protected)
```http
GET /api/test/auth
Authorization: Bearer {your-token}
```

**Response:**
```json
{
    "success": true,
    "message": "Authentication successful!",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token_info": {
        "authenticated": true,
        "token_name": "api-token",
        "token_abilities": []
    },
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

**Use this endpoint to verify your Bearer token is working correctly!**

---

## API Endpoints

### 1. Authentication & User Management

#### Register New User
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Get User Profile (Protected)
```http
GET /api/auth/user
Authorization: Bearer {your-token}
```

#### Refresh Token (Protected)
```http
POST /api/auth/refresh-token
Authorization: Bearer {your-token}
```

#### Logout (Protected)
```http
POST /api/auth/logout
Authorization: Bearer {your-token}
```

---

### 2. Destinations / Places

#### List All Destinations
```http
GET /api/v1/destinations
GET /api/v1/places
```

**Query Parameters:**
- `region` - Filter by region
- `featured=true` - Show only featured places
- `search=keyword` - Search by name
- `sort_by=id` - Sort field
- `sort_order=desc` - Sort direction (asc/desc)
- `per_page=15` - Items per page

**Example:**
```bash
GET /api/v1/destinations?featured=true&per_page=10
```

#### Get Destination Details
```http
GET /api/v1/destinations/{slug}
GET /api/v1/places/{slug}
```

#### Get Destination Gallery
```http
GET /api/v1/destinations/{id}/gallery
```

---

### 3. Packages

#### List All Packages
```http
GET /api/v1/packages
```

**Query Parameters:**
- `place_id` - Filter by destination ID
- `place_slug` - Filter by destination slug
- `category` - Filter by category
- `min_price` - Minimum price
- `max_price` - Maximum price
- `featured=true` - Show only featured
- `search=keyword` - Search by name
- `sort_by`, `sort_order` - Sorting
- `per_page` - Pagination

#### Get Package Details
```http
GET /api/v1/packages/{slug}
```

#### Get Package Gallery
```http
GET /api/v1/packages/{id}/gallery
```

#### Get Packages by Destination
```http
GET /api/v1/packages/destinations?place_id=1
GET /api/v1/packages/destinations?place_slug=paris
```

#### Get Packages by Category
```http
GET /api/v1/packages/category?category=Honeymoon
```

**Required Parameter:**
- `category` - Category name

---

### 4. Services

#### List All Services
```http
GET /api/v1/services
```

#### Get Service Details
```http
GET /api/v1/services/{slug}
```

---

### 5. Attractions

#### List All Attractions
```http
GET /api/v1/attractions
```

**Query Parameters:**
- `place_id` - Filter by destination ID
- `place_slug` - Filter by destination slug
- `featured=true` - Show only featured
- `search=keyword` - Search by name

#### Get Attraction Details
```http
GET /api/v1/attractions/{slug}
```

#### Get Attraction Gallery
```http
GET /api/v1/attractions/{id}/gallery
```

---

### 6. Group Packages

#### List All Group Packages
```http
GET /api/v1/group-packages
```

#### Get Group Package Details
```http
GET /api/v1/group-packages/{slug}
```

#### Get Group Package Gallery
```http
GET /api/v1/group-packages/{id}/gallery
```

---

### 7. Bookings (Protected - Requires Authentication)

#### Get User's Bookings
```http
GET /api/v1/bookings
Authorization: Bearer {your-token}
```

**Query Parameters:**
- `per_page` - Items per page (default: 15)
- Standard pagination parameters

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "package_id": 5,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "travel_date": "2024-06-15",
            "adults": 2,
            "children": 1,
            "status": "pending",
            "payment_status": "pending",
            "total_amount": "1500.00",
            "currency": "USD",
            "package": {...}
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 10
    }
}
```

#### Get Specific Booking
```http
GET /api/v1/bookings/{id}
Authorization: Bearer {your-token}
```

#### Create New Booking (Requires Authentication)
```http
POST /api/v1/bookings
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "package_id": 5,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "travel_date": "2024-06-15",
    "adults": 2,
    "children": 1,
    "customer_address": "123 Main St",
    "customer_city": "New York",
    "customer_country": "USA",
    "addons": ["addon_key_1", "addon_key_2"],
    "addon_services": [1, 2, 3],
    "payment_status": "paid",
    "payment_method": "Credit Card",
    "payment_transaction_id": "TXN123456",
    "payment_amount": 1500.00,
    "contact_method": "whatsapp",
    "whatsapp_number": "+1234567890",
    "notes": "Special requests here"
}
```

**Response:**
```json
{
    "message": "Booking created successfully",
    "data": {
        "id": 1,
        "package_id": 5,
        "name": "John Doe",
        "email": "john@example.com",
        "total_amount": "1500.00",
        "status": "pending",
        ...
    }
}
```

---

### 8. File Upload (Protected - Requires Authentication)

#### Upload Single Image
```http
POST /api/v1/upload/image
Authorization: Bearer {your-token}
Content-Type: multipart/form-data

Form Data:
- image: (file) - Image file (jpeg, png, jpg, gif, webp, max 5MB)
```

**Response:**
```json
{
    "success": true,
    "message": "Image uploaded successfully",
    "data": {
        "url": "https://webcms.tourliz.com/storage/images/1234567890_abc123.jpg",
        "path": "images/1234567890_abc123.jpg",
        "filename": "1234567890_abc123.jpg",
        "original_name": "photo.jpg",
        "size": 245678,
        "mime_type": "image/jpeg",
        "uploaded_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

#### Upload Multiple Images
```http
POST /api/v1/upload/images
Authorization: Bearer {your-token}
Content-Type: multipart/form-data

Form Data:
- images[]: (file) - Multiple image files (jpeg, png, jpg, gif, webp, max 5MB each)
```

**Response:**
```json
{
    "success": true,
    "message": "Images uploaded successfully",
    "data": {
        "images": [
            {
                "url": "https://webcms.tourliz.com/storage/images/1234567890_abc123.jpg",
                "path": "images/1234567890_abc123.jpg",
                "filename": "1234567890_abc123.jpg",
                "original_name": "photo1.jpg",
                "size": 245678,
                "mime_type": "image/jpeg"
            },
            {
                "url": "https://webcms.tourliz.com/storage/images/1234567891_def456.jpg",
                "path": "images/1234567891_def456.jpg",
                "filename": "1234567891_def456.jpg",
                "original_name": "photo2.jpg",
                "size": 312456,
                "mime_type": "image/jpeg"
            }
        ],
        "count": 2,
        "uploaded_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

#### Upload Document/File
```http
POST /api/v1/upload/file
Authorization: Bearer {your-token}
Content-Type: multipart/form-data

Form Data:
- file: (file) - Document file (pdf, doc, docx, xls, xlsx, txt, zip, max 10MB)
```

**Response:**
```json
{
    "success": true,
    "message": "File uploaded successfully",
    "data": {
        "url": "https://webcms.tourliz.com/storage/files/1234567890_document.pdf",
        "path": "files/1234567890_document.pdf",
        "filename": "1234567890_document.pdf",
        "original_name": "document.pdf",
        "size": 524288,
        "mime_type": "application/pdf",
        "extension": "pdf",
        "uploaded_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

**Example using cURL:**
```bash
curl -X POST "https://webcms.tourliz.com/api/v1/upload/image" \
  -H "Authorization: Bearer {your-token}" \
  -F "image=@/path/to/your/image.jpg"
```

**Example using JavaScript (FormData):**
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);

fetch('https://webcms.tourliz.com/api/v1/upload/image', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer {your-token}'
  },
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

**File Upload Constraints:**
- **Images**: jpeg, png, jpg, gif, webp (max 5MB per file)
- **Documents**: pdf, doc, docx, xls, xlsx, txt, zip (max 10MB per file)
- All uploads are stored in `storage/app/public/`
- Files are automatically renamed with timestamp and random string for uniqueness
- Original filename is preserved in response

---

### 9. Currency

#### Get Exchange Rates
```http
GET /api/v1/currency/rates
```

#### Convert Currency
```http
POST /api/v1/currency/convert
Content-Type: application/json

{
    "from": "USD",
    "to": "EUR",
    "amount": 100
}
```

---

## Response Format

### Success Response
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

### Error Response
```json
{
    "message": "Error message",
    "errors": {
        "field": ["Error detail"]
    }
}
```

### Unauthorized (401)
```json
{
    "message": "Unauthenticated."
}
```

---

## Rate Limiting

API requests are rate-limited to prevent abuse. Default limits:
- 60 requests per minute per IP address
- Authenticated users may have higher limits

---

## Notes

- All dates are in ISO 8601 format (YYYY-MM-DD)
- All prices are in decimal format
- Images are returned as full URLs
- Pagination defaults to 15 items per page
- Protected endpoints require `Authorization: Bearer {token}` header

