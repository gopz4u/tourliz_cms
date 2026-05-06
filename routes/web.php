<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\SiteUserController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AttractionController;
use App\Http\Controllers\Admin\GroupPackageController;
use App\Http\Controllers\Admin\CurrencyExchangeRateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\Admin\GroupPackageItineraryController;
use App\Http\Controllers\Admin\WebsiteManagementController;
use App\Http\Controllers\Admin\PackageOfferController;
use App\Http\Controllers\Admin\ReviewController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Storage Route - Serve images when symlink doesn't exist
// MUST be before other routes to catch /storage/* requests first
Route::get('/storage/{path}', [StorageController::class, 'image'])
    ->where('path', '.*')
    ->name('storage.image');

// Diagnostic route (remove after fixing)
Route::get('/git-pull', function () {
    try {
        $output = shell_exec('git pull origin main 2>&1');
        return '<pre>' . $output . '</pre>';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/storage-debug', function () {
    $testFile = 'images/1765304789_3GWdmy3yhV.jpeg';
    $paths = [
        'storage_path' => storage_path('app/public/' . $testFile),
        'base_path' => base_path('storage/app/public/' . $testFile),
        'public_path' => public_path('storage/' . $testFile),
    ];

    $results = [];
    foreach ($paths as $key => $path) {
        $results[$key] = [
            'path' => $path,
            'exists' => file_exists($path),
            'is_file' => is_file($path),
            'readable' => is_readable($path),
        ];
    }

    return response()->json([
        'test_file' => $testFile,
        'paths' => $results,
        'storage_base' => storage_path('app/public'),
        'public_storage' => public_path('storage'),
        'is_symlink' => is_link(public_path('storage')),
        'symlink_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : null,
    ]);
})->name('storage.debug');

// Landing Page
Route::get('/', [\App\Http\Controllers\SiteController::class, 'index'])->name('landing');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout Route (accessible to authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Package Detail Page
Route::get('/packages/{slug}', [PackageController::class, 'show'])->name('packages.show');
Route::post('/packages/{slug}/get-quote', [BookingController::class, 'getQuote'])->name('bookings.get-quote');

// Booking routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/book/package/{id}', [BookingController::class, 'showPackage'])->name('book.package.show');
    Route::post('/book/package', [BookingController::class, 'submitPackage'])->name('book.package.submit');
    Route::post('/book/check-coupon', [BookingController::class, 'checkCoupon'])->name('book.check-coupon');
});

// Admin CMS Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Redirect /admin to dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Modules restricted to Super Admin
    Route::middleware('role:super_admin')->group(function () {
        // Countries Management
        Route::resource('countries', \App\Http\Controllers\Admin\CountryController::class);

        // Destinations Management
        Route::get('/destinations/countries', [DestinationController::class, 'getCountries'])->name('destinations.countries');
        Route::get('/destinations/locations', [DestinationController::class, 'getLocations'])->name('destinations.locations');
        Route::get('/destinations/cities', [DestinationController::class, 'getCities'])->name('destinations.cities');
        Route::resource('destinations', DestinationController::class);

        // Packages Management
        Route::resource('packages', AdminPackageController::class);
        Route::get('/packages/by-destination/{destinationId}', [AdminPackageController::class, 'getByPlace'])->name('packages.by-destination');

        // Services Management
        Route::resource('services', ServiceController::class);

        // Attractions Management
        Route::resource('attractions', AttractionController::class);

        // Group Packages Management
        Route::resource('group-packages', GroupPackageController::class);
        Route::get('/group-packages/by-destination/{destinationId}', [GroupPackageController::class, 'getByPlace'])->name('group-packages.by-destination');

        // Currency Exchange Rates Management
        Route::get('currency-converter', [CurrencyExchangeRateController::class, 'converter'])->name('currency-converter');
        Route::resource('currency-rates', CurrencyExchangeRateController::class);
        Route::post('currency-rates/bulk-update', [CurrencyExchangeRateController::class, 'bulkUpdate'])->name('currency-rates.bulk-update');

        // Users Management
        Route::resource('users', UserController::class);

        // Site Users (public users) Management
        Route::get('site-users', [SiteUserController::class, 'index'])->name('site-users.index');
        Route::post('site-users/{user}/ban', [SiteUserController::class, 'ban'])->name('site-users.ban');
        Route::delete('site-users/{user}', [SiteUserController::class, 'destroy'])->name('site-users.destroy');

        // Inventory Management
        Route::resource('hotels', \App\Http\Controllers\Admin\HotelController::class);
        Route::resource('activities', \App\Http\Controllers\Admin\ActivityController::class);
        Route::resource('transports', \App\Http\Controllers\Admin\TransportController::class);
        Route::resource('entry-tickets', \App\Http\Controllers\Admin\EntryTicketController::class);
        Route::resource('meals', \App\Http\Controllers\Admin\MealController::class);
        Route::resource('tourist-spots', \App\Http\Controllers\Admin\TouristSpotController::class);

        // Supplier Master
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);

        // Coupon Management
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);

        // Website Management
        Route::prefix('website')->name('website.')->group(function () {
            Route::get('/', [WebsiteManagementController::class, 'index'])->name('index');
            Route::post('/settings', [WebsiteManagementController::class, 'updateSettings'])->name('settings.update');
            Route::post('/banners', [WebsiteManagementController::class, 'storeBanner'])->name('banners.store');
            Route::post('/banners/{id}', [WebsiteManagementController::class, 'updateBanner'])->name('banners.update');
            Route::delete('/banners/{id}', [WebsiteManagementController::class, 'destroyBanner'])->name('banners.destroy');
        });

        // Package Offers Management
        Route::resource('package-offers', PackageOfferController::class);
    });

    // Bookings Management
    Route::get('bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');

    Route::get('b2b-itineraries/{id}/whatsapp', [\App\Http\Controllers\Admin\B2BItineraryController::class, 'whatsapp'])->name('b2b-itineraries.whatsapp');

    // Agencies Management
    Route::resource('agencies', \App\Http\Controllers\Admin\AgencyController::class);

    // B2B Itineraries (Admin creates for Agency)
    Route::resource('b2b-itineraries', \App\Http\Controllers\Admin\B2BItineraryController::class);
    Route::get('b2b-itineraries/{id}/pdf', [\App\Http\Controllers\Admin\B2BItineraryController::class, 'pdf'])->name('b2b-itineraries.pdf');

    // B2C Itineraries (Direct/Walk-in Customers)
    Route::resource('b2c-itineraries', \App\Http\Controllers\Admin\B2CItineraryController::class);
    Route::get('b2c-itineraries/{id}/pdf', [\App\Http\Controllers\Admin\B2CItineraryController::class, 'pdf'])->name('b2c-itineraries.pdf');
    Route::get('b2c-itineraries/{id}/whatsapp', [\App\Http\Controllers\Admin\B2CItineraryController::class, 'whatsapp'])->name('b2c-itineraries.whatsapp');

    // Group Itineraries (Dynamic Group Packages)
    Route::resource('group-itineraries', \App\Http\Controllers\Admin\GroupItineraryController::class);
    Route::get('group-itineraries/{id}/pdf', [\App\Http\Controllers\Admin\GroupItineraryController::class, 'pdf'])->name('group-itineraries.pdf');
    Route::get('group-itineraries/{id}/whatsapp', [\App\Http\Controllers\Admin\GroupItineraryController::class, 'whatsapp'])->name('group-itineraries.whatsapp');

    // Itineraries Management (Standard Packages)
    Route::prefix('itineraries')->name('itineraries.')->group(function () {
        Route::get('/', [ItineraryController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [ItineraryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ItineraryController::class, 'update'])->name('update');
        Route::post('/{id}/generate', [ItineraryController::class, 'generate'])->name('generate');
        Route::delete('/{id}', [ItineraryController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [ItineraryController::class, 'preview'])->name('preview');
        Route::get('/{id}/pdf', [ItineraryController::class, 'exportPdf'])->name('pdf');
        Route::post('/{id}/add-day', [ItineraryController::class, 'addDay'])->name('add-day');
        Route::delete('/{id}/day/{dayIndex}', [ItineraryController::class, 'removeDay'])->name('remove-day');
    });

    // Group Package Itineraries Management (Templates)
    Route::prefix('group-package-itineraries')->name('group-package-itineraries.')->group(function () {
        Route::get('/', [GroupPackageItineraryController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [GroupPackageItineraryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GroupPackageItineraryController::class, 'update'])->name('update');
        Route::post('/{id}/generate', [GroupPackageItineraryController::class, 'generate'])->name('generate');
        Route::delete('/{id}', [GroupPackageItineraryController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [GroupPackageItineraryController::class, 'preview'])->name('preview');
        Route::get('/{id}/pdf', [GroupPackageItineraryController::class, 'exportPdf'])->name('pdf');
        Route::post('/{id}/add-day', [GroupPackageItineraryController::class, 'addDay'])->name('add-day');
        Route::delete('/{id}/day/{dayIndex}', [GroupPackageItineraryController::class, 'removeDay'])->name('remove-day');
    });

    // Expenses Management
    Route::get('expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('expenses/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Vendor Sharing
    Route::get('expenses/{id}/whatsapp-vendor', [\App\Http\Controllers\Admin\VendorShareController::class, 'whatsapp'])->name('expenses.whatsapp-vendor');
    Route::get('expenses/{id}/pdf-vendor', [\App\Http\Controllers\Admin\VendorShareController::class, 'pdf'])->name('expenses.pdf-vendor');

    // Upload
    Route::post('/upload/image', [UploadController::class, 'uploadImage'])->name('upload.image');
    Route::post('/upload/images', [UploadController::class, 'uploadMultipleImages'])->name('upload.images');

    // Reviews Management
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{id}/status', [ReviewController::class, 'updateStatus'])->name('reviews.updateStatus');
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
