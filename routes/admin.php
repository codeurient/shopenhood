<?php

use App\Http\Controllers\Admin\AccessSecurityController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BusinessProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryVariantController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LegalController;
use App\Http\Controllers\Admin\ListingApprovalController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\ListingImageController;
use App\Http\Controllers\Admin\ListingTypeController;
use App\Http\Controllers\Admin\ListingVariantController;
use App\Http\Controllers\Admin\LocationCityController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\LoginHistoryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Admin\VariantItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix(config('admin.path'))->name('admin.')->middleware('admin.ip')->group(function () {

    // ========================================================================
    // AUTH ROUTES (Guest Only)
    // ========================================================================
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])
            ->middleware('throttle:admin.login')
            ->name('login.submit');
    });

    // ========================================================================
    // AUTHENTICATED ADMIN ROUTES
    // ========================================================================
    Route::middleware(['auth.admin', 'role:admin', 'log.activity'])->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('update', [ProfileController::class, 'update'])->name('update');
            Route::put('password', [ProfileController::class, 'updatePassword'])->name('password');
        });

        // Logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // ====================================================================
        // CATEGORIES MANAGEMENT
        // ====================================================================
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');

            // Variant management
            Route::get('{category}/variants/available', [CategoryController::class, 'getAvailableVariants'])->name('variants.available');
            Route::post('{category}/variants/sync', [CategoryController::class, 'syncVariants'])->name('variants.sync');

            // Bulk Actions
            Route::post('bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('bulk-activate', [CategoryController::class, 'bulkActivate'])->name('bulk-activate');
            Route::post('bulk-deactivate', [CategoryController::class, 'bulkDeactivate'])->name('bulk-deactivate');

            // Status Toggle
            Route::patch('{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');

            // Tree Operations
            Route::get('tree/view', [CategoryController::class, 'treeView'])->name('tree');
            Route::post('tree/reorder', [CategoryController::class, 'reorderTree'])->name('tree.reorder');

            // AJAX endpoints
            Route::get('ajax/children/{category?}', [CategoryController::class, 'getChildren'])->name('ajax.children');
            Route::get('{category}/variants/available', [CategoryController::class, 'getAvailableVariants'])->name('variants.available');
            Route::post('{category}/variants/sync', [CategoryController::class, 'syncVariants'])->name('variants.sync');

            // AJAX for level-based accordion (index page)
            Route::get('children/{category?}', [CategoryController::class, 'getDirectChildren'])->name('children');

            // Get category hierarchy (for editing listings)
            Route::get('{category}/hierarchy', [CategoryController::class, 'getHierarchy'])->name('hierarchy');

            // ================================================================
            // CATEGORY VARIANTS (Nested Resource)
            // ================================================================
            Route::prefix('{category}/variants')->name('variants.')->group(function () {
                Route::get('/', [CategoryVariantController::class, 'index'])->name('index');
                Route::post('attach', [CategoryVariantController::class, 'attach'])->name('attach');
                Route::delete('{variant}/detach', [CategoryVariantController::class, 'detach'])->name('detach');
                Route::put('{variant}/update-pivot', [CategoryVariantController::class, 'updatePivot'])->name('update-pivot');
                Route::post('sync', [CategoryVariantController::class, 'sync'])->name('sync');
                Route::post('reorder', [CategoryVariantController::class, 'reorder'])->name('reorder');
            });

        });

        // ====================================================================
        // LISTING TYPES MANAGEMENT
        // ====================================================================
        Route::prefix('listing-types')->name('listing-types.')->group(function () {
            Route::get('/', [ListingTypeController::class, 'index'])->name('index');
            Route::get('create', [ListingTypeController::class, 'create'])->name('create');
            Route::post('/', [ListingTypeController::class, 'store'])->name('store');
            Route::get('{listingType}', [ListingTypeController::class, 'show'])->name('show');
            Route::get('{listingType}/edit', [ListingTypeController::class, 'edit'])->name('edit');
            Route::put('{listingType}', [ListingTypeController::class, 'update'])->name('update');
            Route::delete('{listingType}', [ListingTypeController::class, 'destroy'])->name('destroy');

            // Status Toggle
            Route::patch('{listingType}/toggle-status', [ListingTypeController::class, 'toggleStatus'])->name('toggle-status');

            // Bulk Actions
            Route::post('bulk-delete', [ListingTypeController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('reorder', [ListingTypeController::class, 'reorder'])->name('reorder');
        });

        // ====================================================================
        // VARIANTS MANAGEMENT
        // ====================================================================
        Route::prefix('variants')->name('variants.')->group(function () {
            Route::get('/', [VariantController::class, 'index'])->name('index');
            Route::get('create', [VariantController::class, 'create'])->name('create');
            Route::post('/', [VariantController::class, 'store'])->name('store');
            Route::get('{variant}', [VariantController::class, 'show'])->name('show');
            Route::get('{variant}/edit', [VariantController::class, 'edit'])->name('edit');
            Route::put('{variant}', [VariantController::class, 'update'])->name('update');
            Route::delete('{variant}', [VariantController::class, 'destroy'])->name('destroy');

            // Status Toggle
            Route::patch('{variant}/toggle-status', [VariantController::class, 'toggleStatus'])->name('toggle-status');

            // Bulk Actions
            Route::post('bulk-delete', [VariantController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('bulk-activate', [VariantController::class, 'bulkActivate'])->name('bulk-activate');
            Route::post('reorder', [VariantController::class, 'reorder'])->name('reorder');

            // AJAX Helpers
            Route::get('ajax/by-category/{category}', [VariantController::class, 'getByCategory'])->name('ajax.by-category');

            // ================================================================
            // VARIANT ITEMS (Nested Resource)
            // ================================================================
            Route::prefix('{variant}/items')->name('items.')->group(function () {
                Route::get('/', [VariantItemController::class, 'index'])->name('index');
                Route::get('create', [VariantItemController::class, 'create'])->name('create');
                Route::post('/', [VariantItemController::class, 'store'])->name('store');
                Route::get('{variantItem}/edit', [VariantItemController::class, 'edit'])->name('edit');
                Route::put('{variantItem}', [VariantItemController::class, 'update'])->name('update');
                Route::delete('{variantItem}', [VariantItemController::class, 'destroy'])->name('destroy');

                // Status Toggle
                Route::patch('{variantItem}/toggle-status', [VariantItemController::class, 'toggleStatus'])->name('toggle-status');

                // Bulk Actions
                Route::post('bulk-delete', [VariantItemController::class, 'bulkDelete'])->name('bulk-delete');
                Route::post('reorder', [VariantItemController::class, 'reorder'])->name('reorder');
            });
        });

        // ====================================================================
        // LOCATIONS MANAGEMENT (Countries & Cities)
        // ====================================================================
        Route::prefix('locations')->name('locations.')->group(function () {
            Route::get('/', [LocationController::class, 'index'])->name('index');
            Route::get('create', [LocationController::class, 'create'])->name('create');
            Route::post('/', [LocationController::class, 'store'])->name('store');
            Route::get('{location}/edit', [LocationController::class, 'edit'])->name('edit');
            Route::put('{location}', [LocationController::class, 'update'])->name('update');
            Route::delete('{location}', [LocationController::class, 'destroy'])->name('destroy');

            // Status Toggle
            Route::patch('{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('toggle-status');

            // ================================================================
            // CITIES (Nested Resource under Country)
            // ================================================================
            Route::prefix('{location}/cities')->name('cities.')->group(function () {
                Route::get('/', [LocationCityController::class, 'index'])->name('index');
                Route::get('create', [LocationCityController::class, 'create'])->name('create');
                Route::post('/', [LocationCityController::class, 'store'])->name('store');
                Route::get('{city}/edit', [LocationCityController::class, 'edit'])->name('edit');
                Route::put('{city}', [LocationCityController::class, 'update'])->name('update');
                Route::delete('{city}', [LocationCityController::class, 'destroy'])->name('destroy');

                // Status Toggle
                Route::patch('{city}/toggle-status', [LocationCityController::class, 'toggleStatus'])->name('toggle-status');
            });
        });

        // ====================================================================
        // LISTINGS MANAGEMENT
        // ====================================================================
        Route::prefix('listings')->name('listings.')->group(function () {

            // Main CRUD (create/edit restricted to users; admin can only view/moderate)
            Route::get('/', [ListingController::class, 'index'])->name('index');
            Route::get('{listing}', [ListingController::class, 'show'])->name('show');
            Route::delete('{listing}', [ListingController::class, 'destroy'])->name('destroy');

            // AJAX endpoint for loading category variants
            Route::get('category/{category}/variants', [ListingController::class, 'getCategoryVariants'])->name('category.variants');

            // Listing Status Management
            Route::get('pending', [ListingController::class, 'pending'])->name('pending');
            Route::get('active', [ListingController::class, 'active'])->name('active');
            Route::get('expired', [ListingController::class, 'expired'])->name('expired');
            Route::get('rejected', [ListingController::class, 'rejected'])->name('rejected');

            // Approval System
            Route::prefix('{listing}/approval')->name('approval.')->group(function () {
                Route::post('approve', [ListingApprovalController::class, 'approve'])->name('approve');
                Route::post('reject', [ListingApprovalController::class, 'reject'])->name('reject');
            });

            // Listing Lifecycle Management
            Route::patch('{listing}/update-expiration', [ListingController::class, 'updateExpiration'])->name('update-expiration');
            Route::delete('{listing_id}/force-delete', [ListingController::class, 'forceDelete'])->name('force-delete');
            Route::post('{listing_id}/restore', [ListingController::class, 'restore'])->name('restore');

            // Visibility Control
            Route::patch('{listing}/toggle-visibility', [ListingController::class, 'toggleVisibility'])->name('toggle-visibility');
            Route::patch('{listing}/toggle-featured', [ListingController::class, 'toggleFeatured'])->name('toggle-featured');

            // Bulk Actions
            Route::post('bulk-delete', [ListingController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('bulk-force-destroy', [ListingController::class, 'bulkForceDestroy'])->name('bulk-force-destroy');
            Route::post('bulk-restore', [ListingController::class, 'bulkRestore'])->name('bulk-restore');
            Route::post('bulk-approve', [ListingController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('bulk-reject', [ListingController::class, 'bulkReject'])->name('bulk-reject');
            Route::post('bulk-activate', [ListingController::class, 'bulkActivate'])->name('bulk-activate');
            Route::post('bulk-deactivate', [ListingController::class, 'bulkDeactivate'])->name('bulk-deactivate');

            // AJAX Helpers
            Route::get('ajax/filters/{category}', [ListingController::class, 'getCategoryFilters'])->name('ajax.filters');

            // ================================================================
            // LISTING IMAGES (Nested Resource)
            // ================================================================
            Route::prefix('{listing}/images')->name('images.')->group(function () {
                Route::get('/', [ListingImageController::class, 'index'])->name('index');
                Route::post('/', [ListingImageController::class, 'store'])->name('store');
                Route::delete('{image}', [ListingImageController::class, 'destroy'])->name('destroy');
                Route::post('reorder', [ListingImageController::class, 'reorder'])->name('reorder');
                Route::patch('{image}/set-primary', [ListingImageController::class, 'setPrimary'])->name('set-primary');
                Route::post('bulk-upload', [ListingImageController::class, 'bulkUpload'])->name('bulk-upload');
                Route::post('bulk-delete', [ListingImageController::class, 'bulkDelete'])->name('bulk-delete');
            });

            // ================================================================
            // LISTING VARIANTS (Nested Resource)
            // ================================================================
            Route::prefix('{listing}/variants')->name('variants.')->group(function () {
                Route::get('/', [ListingVariantController::class, 'index'])->name('index');
                Route::post('attach', [ListingVariantController::class, 'attach'])->name('attach');
                Route::put('{listingVariant}', [ListingVariantController::class, 'update'])->name('update');
                Route::delete('{listingVariant}', [ListingVariantController::class, 'destroy'])->name('destroy');
                Route::post('sync', [ListingVariantController::class, 'sync'])->name('sync');
            });

            // ================================================================
            // PRODUCT VARIATIONS (Nested Resource)
            // ================================================================
            Route::prefix('{listing}/variations')->name('variations.')->group(function () {
                Route::get('/', [ProductVariationController::class, 'index'])->name('index');
                Route::get('create', [ProductVariationController::class, 'create'])->name('create');
                Route::post('/', [ProductVariationController::class, 'store'])->name('store');
                Route::get('{variation}/edit', [ProductVariationController::class, 'edit'])->name('edit');
                Route::put('{variation}', [ProductVariationController::class, 'update'])->name('update');
                Route::delete('{variation}', [ProductVariationController::class, 'destroy'])->name('destroy');

                // Stock Management
                Route::patch('{variation}/toggle-availability', [ProductVariationController::class, 'toggleAvailability'])->name('toggle-availability');
                Route::patch('{variation}/update-stock', [ProductVariationController::class, 'updateStock'])->name('update-stock');

                // Bulk Actions
                Route::post('bulk-delete', [ProductVariationController::class, 'bulkDelete'])->name('bulk-delete');
                Route::post('bulk-update-stock', [ProductVariationController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
                Route::post('generate', [ProductVariationController::class, 'generate'])->name('generate');
            });
        });

        // ====================================================================
        // USER MANAGEMENT
        // ====================================================================
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // ====================================================================
        // BUSINESS PROFILES MANAGEMENT
        // ====================================================================
        Route::prefix('business-profiles')->name('business-profiles.')->group(function () {
            Route::get('/', [BusinessProfileController::class, 'index'])->name('index');
            Route::get('/create/{user}', [BusinessProfileController::class, 'create'])->name('create');
            Route::post('/store/{user}', [BusinessProfileController::class, 'store'])->name('store');
            Route::get('/{businessProfile}', [BusinessProfileController::class, 'show'])->name('show');
            Route::get('/{businessProfile}/edit', [BusinessProfileController::class, 'edit'])->name('edit');
            Route::put('/{businessProfile}', [BusinessProfileController::class, 'update'])->name('update');
            Route::delete('/{businessProfile}', [BusinessProfileController::class, 'destroy'])->name('destroy');
            Route::post('/{businessProfile}/approve', [BusinessProfileController::class, 'approve'])->name('approve');
            Route::post('/{businessProfile}/revoke', [BusinessProfileController::class, 'revoke'])->name('revoke');
            Route::post('/{businessProfile}/confident-seller/approve', [BusinessProfileController::class, 'approveConfidentSeller'])->name('confident-seller.approve');
            Route::post('/{businessProfile}/confident-seller/reject', [BusinessProfileController::class, 'rejectConfidentSeller'])->name('confident-seller.reject');
        });

        // ====================================================================
        // SLIDERS & BANNERS MANAGEMENT
        // ====================================================================
        Route::prefix('sliders')->name('sliders.')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('index');
            Route::get('/create', [SliderController::class, 'create'])->name('create');
            Route::post('/', [SliderController::class, 'store'])->name('store');
            Route::get('/{slider}/edit', [SliderController::class, 'edit'])->name('edit');
            Route::put('/{slider}', [SliderController::class, 'update'])->name('update');
            Route::delete('/{slider}', [SliderController::class, 'destroy'])->name('destroy');
            Route::post('/update-order', [SliderController::class, 'updateOrder'])->name('update-order');
        });

        // ====================================================================
        // COUPONS MANAGEMENT
        // ====================================================================
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('create', [CouponController::class, 'create'])->name('create');
            Route::post('/', [CouponController::class, 'store'])->name('store');
            Route::get('{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
            Route::put('{coupon}', [CouponController::class, 'update'])->name('update');
            Route::delete('{coupon}', [CouponController::class, 'destroy'])->name('destroy');
            Route::patch('{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('toggle-status');
        });

        // ====================================================================
        // SETTINGS
        // ====================================================================
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('/', [SettingController::class, 'update'])->name('update');
        });

        // ====================================================================
        // ACCESS SECURITY (Admin Path Management)
        // ====================================================================
        Route::prefix('access-security')->name('access-security.')->group(function () {
            Route::get('/', [AccessSecurityController::class, 'index'])->name('index');
            Route::put('/', [AccessSecurityController::class, 'update'])->name('update');
            Route::post('/generate', [AccessSecurityController::class, 'generate'])->name('generate');
            Route::delete('/reset', [AccessSecurityController::class, 'reset'])->name('reset');
        });

        // ====================================================================
        // SEO / AEO / GEO SETTINGS
        // ====================================================================
        Route::prefix('seo')->name('seo.')->group(function () {
            Route::get('/', [AdminSeoController::class, 'index'])->name('index');
            Route::put('/', [AdminSeoController::class, 'update'])->name('update');
        });

        // ====================================================================
        // ACTIVITY LOGS
        // ====================================================================
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('{activity}', [ActivityLogController::class, 'show'])->name('show');
            Route::delete('clear-old', [ActivityLogController::class, 'clearOld'])->name('clear-old');
            Route::delete('clear-all', [ActivityLogController::class, 'clearAll'])->name('clear-all');
        });

        // ====================================================================
        // LOGIN HISTORIES
        // ====================================================================
        Route::prefix('login-histories')->name('login-histories.')->group(function () {
            Route::get('/', [LoginHistoryController::class, 'index'])->name('index');
            Route::get('{loginHistory}', [LoginHistoryController::class, 'show'])->name('show');
            Route::get('user/{user}', [LoginHistoryController::class, 'userHistory'])->name('user');
            Route::post('block-ip', [LoginHistoryController::class, 'blockIp'])->name('block-ip');
            Route::delete('clear-all', [LoginHistoryController::class, 'clearAll'])->name('clear-all');
        });

        // ====================================================================
        // LEGAL & POLICIES
        // ====================================================================
        Route::prefix('legal')->name('legal.')->group(function () {
            Route::get('/', [LegalController::class, 'index'])->name('index');
            Route::get('create', [LegalController::class, 'create'])->name('create');
            Route::post('/', [LegalController::class, 'store'])->name('store');
            Route::get('{policy}', [LegalController::class, 'show'])->name('show');
            Route::get('{policy}/edit', [LegalController::class, 'edit'])->name('edit');
            Route::put('{policy}', [LegalController::class, 'update'])->name('update');
            Route::delete('{policy}', [LegalController::class, 'destroy'])->name('destroy');
            Route::patch('{policy}/toggle-status', [LegalController::class, 'toggleStatus'])->name('toggle-status');
        });

        // ====================================================================
        // BLOG POSTS
        // ====================================================================
        Route::prefix('blog-posts')->name('blog-posts.')->group(function () {
            Route::get('/', [BlogPostController::class, 'index'])->name('index');
            Route::get('create', [BlogPostController::class, 'create'])->name('create');
            Route::post('/', [BlogPostController::class, 'store'])->name('store');
            Route::get('{blogPost}/edit', [BlogPostController::class, 'edit'])->name('edit');
            Route::put('{blogPost}', [BlogPostController::class, 'update'])->name('update');
            Route::delete('{blogPost}', [BlogPostController::class, 'destroy'])->name('destroy');
            Route::patch('{blogPost}/toggle-visibility', [BlogPostController::class, 'toggleVisibility'])->name('toggle-visibility');
        });

        // ====================================================================
        // PAGES (About, Privacy Policy, Payment Services, etc.)
        // ====================================================================
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::post('reorder', [PageController::class, 'reorder'])->name('reorder');
            Route::get('{page}/edit', [PageController::class, 'edit'])->name('edit');
            Route::put('{page}', [PageController::class, 'update'])->name('update');
            Route::delete('{page}', [PageController::class, 'destroy'])->name('destroy');
            Route::patch('{page}/toggle-visibility', [PageController::class, 'toggleVisibility'])->name('toggle-visibility');
        });

        // ====================================================================
        // STOCK MANAGEMENT
        // ====================================================================
        Route::prefix('stock')->name('stock.')->group(function () {
            // Dashboard
            Route::get('/', [StockManagementController::class, 'index'])->name('index');

            // Stock Adjustment
            Route::get('{variation}/edit', [StockManagementController::class, 'edit'])->name('edit');
            Route::post('{variation}/adjust', [StockManagementController::class, 'adjust'])->name('adjust');

            // Movement History
            Route::get('history', [StockManagementController::class, 'history'])->name('history');

            // Low Stock Alerts
            Route::get('low-stock-alerts', [StockManagementController::class, 'lowStockAlerts'])->name('low-stock-alerts');

            // Bulk Update
            Route::post('bulk-update', [StockManagementController::class, 'bulkUpdate'])->name('bulk-update');

            // Export
            Route::get('export', [StockManagementController::class, 'export'])->name('export');
        });

    });
});
