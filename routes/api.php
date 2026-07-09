<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AttractionController;
use App\Http\Controllers\Api\GroupPackageController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\B2BBookingController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ============================================
// 0. API Diagnostic Route (for troubleshooting)
// ============================================
Route::get('/diagnostic', function () {
    return response()->json([
        'api_routes_loaded' => true,
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
        'timestamp' => now()->toISOString(),
        'test_endpoints' => [
            'public_test' => url('/api/test/public'),
            'auth_login' => url('/api/auth/login'),
            'packages' => url('/api/v1/packages'),
        ],
    ]);
});

// ============================================
// 1. Authentication & User Management APIs
// ============================================
Route::prefix('auth')->group(function () {
    // Public routes
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/devices', [AuthController::class, 'devices']);
        Route::delete('/devices/{tokenId}', [AuthController::class, 'revokeDevice']);
        Route::post('/devices/revoke-others', [AuthController::class, 'revokeOtherDevices']);
    });
});

// ============================================
// 2. Test Endpoints
// ============================================
Route::prefix('test')->group(function () {
    // Public test endpoint
    Route::get('/public', [TestController::class, 'testPublic']);

    // Protected test endpoint (requires authentication)
    Route::middleware('auth:sanctum')->get('/auth', [TestController::class, 'testAuth']);
});

// ============================================
// 3. Public API Routes (v1) - No authentication required
// ============================================
Route::prefix('v1')->group(function () {

    // Destinations / Places API
    Route::prefix('destinations')->group(function () {
        Route::get('/', [DestinationController::class, 'index']);
        Route::get('/{slug}', [DestinationController::class, 'show']);
        Route::get('/{id}/gallery', [DestinationController::class, 'gallery']);
    });

    // Packages API
    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::get('/destinations', [PackageController::class, 'destinations']);
        Route::get('/category', [PackageController::class, 'category']);
        Route::get('/{slug}/itinerary', [PackageController::class, 'getItinerary']);
        Route::post('/{slug}/itinerary/generate', [PackageController::class, 'generateSampleItinerary']);
        Route::get('/{id}/gallery', [PackageController::class, 'gallery']);
        Route::get('/{slug}', [PackageController::class, 'show']);
    });

    // Services API
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::get('/{slug}', [ServiceController::class, 'show']);
    });

    // Attractions API
    Route::prefix('attractions')->group(function () {
        Route::get('/', [AttractionController::class, 'index']);
        Route::get('/{slug}', [AttractionController::class, 'show']);
        Route::get('/{id}/gallery', [AttractionController::class, 'gallery']);
    });

    // Group Packages API
    Route::prefix('group-packages')->group(function () {
        Route::get('/', [GroupPackageController::class, 'index']);
        Route::get('/{slug}', [GroupPackageController::class, 'show']);
        Route::get('/{id}/gallery', [GroupPackageController::class, 'gallery']);
    });

    // Currency API
    Route::prefix('currency')->group(function () {
        Route::get('/rates', [CurrencyController::class, 'getRates']);
        Route::post('/convert', [CurrencyController::class, 'convert']);
    });

    // Legacy routes (for backward compatibility)
    Route::get('/places', [DestinationController::class, 'index']);
    Route::get('/places/{slug}', [DestinationController::class, 'show']);
});

// ============================================
// 4. Protected User-Specific APIs (Require Authentication OR API Key)
// ============================================
// Option 1: Use API Key (no login required) - Set API_KEY in .env
// Option 2: Use Bearer Token (login required) - Use /api/auth/login
Route::middleware(['api.key'])->prefix('v1')->group(function () {
    // Bookings API (API Key or Bearer Token)
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']); // Get user's bookings
        Route::post('/', [BookingController::class, 'store']); // Create new booking
        Route::get('/{id}', [BookingController::class, 'show']); // Get specific booking
    });

    // File Upload API (API Key or Bearer Token)
    Route::prefix('upload')->group(function () {
        Route::post('/image', [UploadController::class, 'uploadImage']); // Upload single image
        Route::post('/images', [UploadController::class, 'uploadMultipleImages']); // Upload multiple images
        Route::post('/file', [UploadController::class, 'uploadFile']); // Upload document/file
    });
});

// Alternative: Routes that require Bearer Token (login) only
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Add routes here that specifically require user login
});

// ============================================
// 5. B2B Bookings API (Token-based)
// ============================================
// ============================================
// 6. Inventory API (For Itinerary Builder)
// ============================================
Route::prefix('inventory')->group(function () {
    Route::get('hotels', [\App\Http\Controllers\Api\InventoryApiController::class, 'hotels']);
    Route::get('activities', [\App\Http\Controllers\Api\InventoryApiController::class, 'activities']);
    Route::get('transports', [\App\Http\Controllers\Api\InventoryApiController::class, 'transports']);
    Route::get('tickets', [\App\Http\Controllers\Api\InventoryApiController::class, 'tickets']);
    Route::get('meals', [\App\Http\Controllers\Api\InventoryApiController::class, 'meals']);
    Route::get('spots', [\App\Http\Controllers\Api\InventoryApiController::class, 'spots']);
    Route::get('suppliers/{id}/assets', [\App\Http\Controllers\Api\InventoryApiController::class, 'supplierAssets']);
});

Route::middleware('auth:sanctum')->prefix('b2b')->group(function () {
    // Custom Itinerary Routes
    Route::get('/itineraries', [\App\Http\Controllers\Api\B2B\ItineraryController::class, 'index']);
    Route::post('/itineraries', [\App\Http\Controllers\Api\B2B\ItineraryController::class, 'store']);
    Route::get('/itineraries/{id}', [\App\Http\Controllers\Api\B2B\ItineraryController::class, 'show']);
    Route::put('/itineraries/{id}', [\App\Http\Controllers\Api\B2B\ItineraryController::class, 'update']);
    Route::delete('/itineraries/{id}', [\App\Http\Controllers\Api\B2B\ItineraryController::class, 'destroy']);
    Route::get('/itineraries/{id}/pdf', [\App\Http\Controllers\Api\B2B\ItineraryController::class, 'generatePdf']);

    Route::get('/bookings', [\App\Http\Controllers\Api\B2BBookingController::class, 'index']);
});
