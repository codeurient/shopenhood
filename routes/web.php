<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
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
Route::get('/api/locations/countries-cities', function () {
    $countries = \App\Models\Location::query()
        ->where('type', 'country')
        ->where('is_active', true)
        ->with(['cities' => function ($q) {
            $q->where('is_active', true)->orderBy('name');
        }])
        ->orderBy('name')
        ->get();

    $result = [];
    foreach ($countries as $country) {
        $result[$country->name] = $country->cities->pluck('name')->toArray();
    }

    return response()->json($result);
})->name('api.locations.countries-cities');

// Public Listing Routes
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/listings/{listing}/available-options', [ListingController::class, 'getAvailableOptions'])->name('listings.available-options');
Route::get('/variations/{variation}', [ListingController::class, 'getVariation'])->name('variations.show');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

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
});

require __DIR__.'/auth.php';
