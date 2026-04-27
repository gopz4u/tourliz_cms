# Multiple User and Device Login System

## Overview
This system supports **multiple users** logging in simultaneously, and each user can be logged in from **multiple devices** at the same time.

## Features

### ✅ Multiple Users
- Different users can log in simultaneously without affecting each other
- Each user maintains their own independent sessions and tokens

### ✅ Multiple Devices Per User
- Each user can be logged in from multiple devices (phone, tablet, desktop, etc.)
- Each device gets its own unique token/session
- Devices are tracked with device name, device ID, IP address, and user agent

## API Endpoints

### Authentication Endpoints

#### 1. Register User
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "device_name": "iPhone 13",          // Optional
    "device_id": "unique-device-id"      // Optional
}
```

**Response:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "1|abc123xyz789...",
    "device": {
        "name": "iPhone 13",
        "id": "unique-device-id"
    }
}
```

#### 2. Login User
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123",
    "device_name": "iPhone 13",          // Optional
    "device_id": "unique-device-id"      // Optional
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "1|abc123xyz789...",
    "device": {
        "name": "iPhone 13",
        "id": "unique-device-id"
    }
}
```

**Note:** Multiple login requests from different devices will create separate tokens. Existing tokens are NOT revoked.

#### 3. Get User Profile
```http
GET /api/auth/user
Authorization: Bearer {token}
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2024-01-01T00:00:00.000000Z"
    },
    "current_device": {
        "name": "iPhone 13",
        "id": "unique-device-id",
        "ip_address": "192.168.1.1",
        "last_used_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

#### 4. List All Active Devices
```http
GET /api/auth/devices
Authorization: Bearer {token}
```

**Response:**
```json
{
    "devices": [
        {
            "id": 1,
            "name": "iPhone 13",
            "device_id": "unique-device-id-1",
            "ip_address": "192.168.1.1",
            "user_agent": "Mozilla/5.0...",
            "last_used_at": "2024-01-01T12:00:00.000000Z",
            "created_at": "2024-01-01T10:00:00.000000Z",
            "is_current_device": true
        },
        {
            "id": 2,
            "name": "Desktop PC",
            "device_id": "unique-device-id-2",
            "ip_address": "192.168.1.2",
            "user_agent": "Mozilla/5.0...",
            "last_used_at": "2024-01-01T11:00:00.000000Z",
            "created_at": "2024-01-01T09:00:00.000000Z",
            "is_current_device": false
        }
    ],
    "total_devices": 2
}
```

#### 5. Revoke Specific Device
```http
DELETE /api/auth/devices/{tokenId}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Device logged out successfully"
}
```

#### 6. Revoke All Other Devices (Keep Current)
```http
POST /api/auth/devices/revoke-others
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "All other devices logged out successfully",
    "revoked_count": 2
}
```

#### 7. Logout Current Device
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

## Device Information

### Automatic Detection
If `device_name` and `device_id` are not provided in the request:
- **device_name**: Extracted from `X-Device-Name` header, or defaults to "Unknown Device"
- **device_id**: Extracted from `X-Device-ID` header, or auto-generated UUID
- **ip_address**: Automatically detected from request
- **user_agent**: Automatically extracted from request headers

### Manual Specification
You can provide device information in two ways:

1. **Request Body** (for POST requests):
```json
{
    "device_name": "My iPhone",
    "device_id": "iphone-12345"
}
```

2. **Request Headers**:
```http
X-Device-Name: My iPhone
X-Device-ID: iphone-12345
```

## Web Session Support

For web-based logins (non-API), device information is automatically stored in the session:
- Device ID is stored in session
- Device info (name, IP, user agent) is tracked
- Multiple browser sessions from different devices are supported

## Database Schema

The `personal_access_tokens` table includes:
- `device_name` - Name of the device
- `device_id` - Unique identifier for the device
- `ip_address` - IP address when token was created
- `user_agent` - Browser/client user agent string

## Usage Examples

### Example 1: Login from Multiple Devices

**Device 1 (iPhone):**
```bash
curl -X POST https://your-api.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123",
    "device_name": "iPhone 13",
    "device_id": "iphone-abc123"
  }'
```

**Device 2 (Desktop):**
```bash
curl -X POST https://your-api.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123",
    "device_name": "Desktop PC",
    "device_id": "desktop-xyz789"
  }'
```

Both devices will receive different tokens and can be used simultaneously.

### Example 2: Check Active Devices

```bash
curl -X GET https://your-api.com/api/auth/devices \
  -H "Authorization: Bearer {token}"
```

### Example 3: Logout from Specific Device

```bash
curl -X DELETE https://your-api.com/api/auth/devices/2 \
  -H "Authorization: Bearer {token}"
```

## Security Features

1. **Session Regeneration**: Web sessions are regenerated on login for security
2. **Token Isolation**: Each device has its own isolated token
3. **Device Tracking**: IP address and user agent are logged for security auditing
4. **Selective Logout**: Users can logout from specific devices without affecting others

## Migration

The database migration has been run automatically. The `personal_access_tokens` table now includes device tracking fields.

## Notes

- **No Token Revocation on Login**: Unlike single-device systems, logging in from a new device does NOT revoke existing tokens
- **Unlimited Devices**: There's no hard limit on the number of devices per user (though you may want to add one for security)
- **Token Expiration**: Tokens follow Laravel Sanctum's default expiration rules
- **Web Sessions**: Web-based logins use Laravel's session system, which naturally supports multiple devices
