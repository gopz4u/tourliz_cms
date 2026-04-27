# How to Get and Use API Token

## Step 1: Get Your Token (Login)

### Endpoint Location
```
POST https://webcms.tourliz.com/api/auth/login
```

### Request Body
```json
{
    "email": "your-email@example.com",
    "password": "your-password"
}
```

### Response (You'll get the token here)
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

**The `token` field contains your Bearer token!**

---

## Step 2: Use the Token (Location)

### Token Location: HTTP Header
The token must be placed in the **`Authorization`** header of your HTTP request.

### Format
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Important:** 
- Header name: `Authorization`
- Format: `Bearer ` (with space) followed by your token
- Example: `Authorization: Bearer 1|abc123xyz789token...`

---

## Examples

### 1. Using cURL (Command Line)

**Get Token:**
```bash
curl -X POST "https://webcms.tourliz.com/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your-email@example.com",
    "password": "your-password"
  }'
```

**Use Token (Example - Get Bookings):**
```bash
curl -X GET "https://webcms.tourliz.com/api/v1/bookings" \
  -H "Authorization: Bearer 1|abc123xyz789token..." \
  -H "Accept: application/json"
```

**Use Token (Example - Get Packages):**
```bash
curl -X GET "https://webcms.tourliz.com/api/v1/packages" \
  -H "Authorization: Bearer 1|abc123xyz789token..." \
  -H "Accept: application/json"
```

---

### 2. Using Postman

**Step 1: Get Token**
1. Create new request
2. Method: `POST`
3. URL: `https://webcms.tourliz.com/api/auth/login`
4. Go to **Body** tab → Select **raw** → Choose **JSON**
5. Enter:
   ```json
   {
       "email": "your-email@example.com",
       "password": "your-password"
   }
   ```
6. Click **Send**
7. Copy the `token` value from response

**Step 2: Use Token**
1. Create new request (e.g., GET bookings)
2. Method: `GET`
3. URL: `https://webcms.tourliz.com/api/v1/bookings`
4. Go to **Authorization** tab
5. Type: Select **Bearer Token**
6. Token: Paste your token (e.g., `1|abc123xyz789token...`)
7. Click **Send**

---

### 3. Using JavaScript (Fetch API)

**Get Token:**
```javascript
// Login to get token
fetch('https://webcms.tourliz.com/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'your-email@example.com',
    password: 'your-password'
  })
})
.then(response => response.json())
.then(data => {
  const token = data.token; // Save this token!
  console.log('Token:', token);
  
  // Store token (e.g., in localStorage)
  localStorage.setItem('api_token', token);
});
```

**Use Token:**
```javascript
// Get token from storage
const token = localStorage.getItem('api_token');

// Make authenticated request
fetch('https://webcms.tourliz.com/api/v1/bookings', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  console.log('Bookings:', data);
});
```

---

### 4. Using JavaScript (Axios)

**Get Token:**
```javascript
import axios from 'axios';

// Login to get token
axios.post('https://webcms.tourliz.com/api/auth/login', {
  email: 'your-email@example.com',
  password: 'your-password'
})
.then(response => {
  const token = response.data.token;
  console.log('Token:', token);
  
  // Store token
  localStorage.setItem('api_token', token);
});
```

**Use Token:**
```javascript
import axios from 'axios';

const token = localStorage.getItem('api_token');

// Make authenticated request
axios.get('https://webcms.tourliz.com/api/v1/bookings', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => {
  console.log('Bookings:', response.data);
});
```

---

### 5. Using PHP

**Get Token:**
```php
<?php
$ch = curl_init('https://webcms.tourliz.com/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'your-email@example.com',
    'password' => 'your-password'
]));

$response = curl_exec($ch);
$data = json_decode($response, true);
$token = $data['token']; // Save this token!
curl_close($ch);
```

**Use Token:**
```php
<?php
$token = '1|abc123xyz789token...'; // Your token

$ch = curl_init('https://webcms.tourliz.com/api/v1/bookings');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$bookings = json_decode($response, true);
curl_close($ch);

print_r($bookings);
```

---

### 6. Using Python (requests library)

**Get Token:**
```python
import requests

# Login to get token
response = requests.post(
    'https://webcms.tourliz.com/api/auth/login',
    json={
        'email': 'your-email@example.com',
        'password': 'your-password'
    },
    headers={'Content-Type': 'application/json'}
)

data = response.json()
token = data['token']  # Save this token!
print(f'Token: {token}')
```

**Use Token:**
```python
import requests

token = '1|abc123xyz789token...'  # Your token

# Make authenticated request
response = requests.get(
    'https://webcms.tourliz.com/api/v1/bookings',
    headers={
        'Authorization': f'Bearer {token}',
        'Accept': 'application/json'
    }
)

bookings = response.json()
print(bookings)
```

---

## Protected Endpoints (Require Token)

These endpoints require the `Authorization: Bearer {token}` header:

- `GET /api/v1/bookings` - Get user's bookings
- `POST /api/v1/bookings` - Create new booking
- `GET /api/v1/bookings/{id}` - Get specific booking
- `POST /api/v1/upload/image` - Upload image
- `POST /api/v1/upload/images` - Upload multiple images
- `POST /api/v1/upload/file` - Upload file
- `GET /api/auth/user` - Get user profile
- `POST /api/auth/logout` - Logout
- `POST /api/auth/refresh-token` - Refresh token
- `GET /api/test/auth` - Test authentication

---

## Public Endpoints (No Token Required)

These endpoints don't require authentication:

- `GET /api/test/public` - Test public endpoint
- `POST /api/auth/login` - Login (to get token)
- `POST /api/auth/register` - Register new user
- `GET /api/v1/packages` - List packages
- `GET /api/v1/packages/{slug}` - Get package details
- `GET /api/v1/destinations` - List destinations
- `GET /api/v1/services` - List services
- `GET /api/v1/attractions` - List attractions
- `GET /api/v1/currency/rates` - Get currency rates

---

## Token Format

Your token will look like this:
```
1|abc123xyz789token...
```

**Format:** `{id}|{random_string}`

- The `|` (pipe) character is part of the token
- Include the entire string including the `|`
- Example: `1|abc123xyz789token...`

---

## Quick Test

**1. Get Token:**
```bash
curl -X POST "https://webcms.tourliz.com/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"your-email@example.com","password":"your-password"}'
```

**2. Copy the token from response**

**3. Test with token:**
```bash
curl -X GET "https://webcms.tourliz.com/api/test/auth" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

If successful, you'll get:
```json
{
    "success": true,
    "message": "Authentication successful!",
    "user": {...}
}
```

---

## Important Notes

1. **Token Storage:** Store your token securely (don't expose it in client-side code if possible)
2. **Token Expiration:** Tokens don't expire by default, but you can refresh them using `/api/auth/refresh-token`
3. **Logout:** Use `/api/auth/logout` to revoke the current token
4. **Header Format:** Always use `Bearer ` (with space) before the token
5. **HTTPS:** Always use HTTPS in production (your API already uses HTTPS)

---

## Troubleshooting

### Error: "Unauthenticated"
- Check if token is correct
- Verify header format: `Authorization: Bearer {token}`
- Make sure there's a space after "Bearer"

### Error: "Token not found"
- Token might be expired or revoked
- Try logging in again to get a new token

### Error: Redirecting to login page
- This should be fixed now, but if it happens:
  - Make sure you're using the correct endpoint
  - Check if you're accessing a protected endpoint
  - Verify the `Accept: application/json` header is set

