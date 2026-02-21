<?php

use App\Http\Controllers\Business\ListingController as BusinessListingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\AddressController as UserAddressController;
use App\Http\Controllers\User\BusinessProfileController as UserBusinessProfileController;
use App\Http\Controllers\User\CouponController as UserCouponController;
use App\Http\Controllers\User\ListingController as UserListingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public API Routes
Route::get('/api/categories/children/{category?}', [CategoryController::class, 'getChildren'])->name('api.categories.children');
Route::get('/api/categories/{category}/variants', [CategoryController::class, 'getVariants'])->name('api.categories.variants');
Route::get('/api/categories/{category}/hierarchy', [CategoryController::class, 'getHierarchy'])->name('api.categories.hierarchy');
Route::get('/api/locations/countries', function () {
    return response()->json([
        'success' => true,
        'countries' => \App\Models\Location::query()
            ->where('type', 'country')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']),
    ]);
})->name('api.locations.countries');

Route::get('/api/locations/{country}/cities', function (\App\Models\Location $country) {
    return response()->json([
        'success' => true,
        'cities' => $country->children()
            ->where('type', 'city')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']),
    ]);
})->name('api.locations.cities');

// Public Listing Routes
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing:slug}', [ListingController::class, 'show'])
    ->where('listing', '[a-z0-9-]+')
    ->name('listings.show');
Route::post('/listings/{listing}/available-options', [ListingController::class, 'getAvailableOptions'])
    ->middleware('throttle:60,1')
    ->name('listings.available-options');
Route::get('/variations/{variation}', [ListingController::class, 'getVariation'])
    ->where('variation', '[0-9]+')
    ->name('variations.show');

// Listing Reviews (auth required, rate limited)
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/listings/{listing}/reviews', [ListingReviewController::class, 'store'])->name('listings.reviews.store');
    Route::delete('/reviews/{review}', [ListingReviewController::class, 'destroy'])
        ->where('review', '[0-9]+')
        ->name('listings.reviews.destroy');
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // User Coupon Management
    Route::prefix('my-coupons')->name('user.coupons.')->group(function () {
        Route::get('/', [UserCouponController::class, 'index'])->name('index');
        Route::get('/create', [UserCouponController::class, 'create'])->name('create');
        Route::post('/', [UserCouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [UserCouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [UserCouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [UserCouponController::class, 'destroy'])->name('destroy');
        Route::patch('/{coupon}/toggle-status', [UserCouponController::class, 'toggleStatus'])->name('toggle-status');
    });

    // User Listing Management
    Route::prefix('my-listings')->name('user.listings.')->group(function () {
        Route::get('/', [UserListingController::class, 'index'])->name('index');
        Route::get('/create', [UserListingController::class, 'create'])->name('create');
        Route::post('/', [UserListingController::class, 'store'])->name('store');
        Route::get('/{listing}', [UserListingController::class, 'show'])->name('show');
        Route::get('/{listing}/edit', [UserListingController::class, 'edit'])->name('edit');
        Route::put('/{listing}', [UserListingController::class, 'update'])->name('update');
        Route::patch('/{listing}/toggle', [UserListingController::class, 'toggleVisibility'])->name('toggle');
        Route::delete('/{listing}', [UserListingController::class, 'destroy'])->name('destroy');
        Route::delete('/{listing_id}/force', [UserListingController::class, 'forceDestroy'])->name('force-destroy');
        Route::post('/{listing_id}/reshare', [UserListingController::class, 'reshare'])->name('reshare');
    });

    // User Address Management
    Route::prefix('my-addresses')->name('user.addresses.')->group(function () {
        Route::get('/', [UserAddressController::class, 'index'])->name('index');
        Route::get('/create', [UserAddressController::class, 'create'])->name('create');
        Route::post('/', [UserAddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [UserAddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [UserAddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [UserAddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/set-default', [UserAddressController::class, 'setDefault'])->name('set-default');
    });

    // API: User Addresses (for checkout)
    Route::get('/api/user/addresses', [UserAddressController::class, 'getAddresses'])->name('api.user.addresses');
    Route::get('/api/user/addresses/{address}', [UserAddressController::class, 'getAddress'])->name('api.user.address');

    // Business Profile & Listings
    Route::prefix('business')->name('business.')->group(function () {
        // Business profile (existing)
        Route::get('/profile', [UserBusinessProfileController::class, 'show'])->name('profile');
        Route::get('/profile/create', [UserBusinessProfileController::class, 'create'])->name('profile.create');
        Route::post('/profile', [UserBusinessProfileController::class, 'store'])->name('profile.store');
        Route::get('/profile/edit', [UserBusinessProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [UserBusinessProfileController::class, 'update'])->name('profile.update');

        // Business listings
        Route::prefix('listings')->name('listings.')->group(function () {
            // Index is accessible to all authenticated users (shows upgrade info if not a business user)
            Route::get('/', [BusinessListingController::class, 'index'])->name('index');

            // All write/management routes require an active business subscription
            Route::middleware('business.user')->group(function () {
                Route::get('/create', [BusinessListingController::class, 'create'])->name('create');
                Route::post('/', [BusinessListingController::class, 'store'])->name('store');
                Route::get('/{listing}/edit', [BusinessListingController::class, 'edit'])->name('edit');
                Route::put('/{listing}', [BusinessListingController::class, 'update'])->name('update');
                Route::patch('/{listing}/toggle', [BusinessListingController::class, 'toggleVisibility'])->name('toggle');
                Route::delete('/{listing}', [BusinessListingController::class, 'destroy'])->name('destroy');
                Route::delete('/{listing_id}/force', [BusinessListingController::class, 'forceDestroy'])->name('force-destroy');
                Route::post('/{listing_id}/reshare', [BusinessListingController::class, 'reshare'])->name('reshare');
            });
        });
    });
});

require __DIR__.'/auth.php';
