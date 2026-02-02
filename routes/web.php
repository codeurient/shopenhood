<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Listing Routes
Route::get('/listings/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/listings/{listing}/available-options', [ListingController::class, 'getAvailableOptions'])->name('listings.available-options');
Route::get('/variations/{variation}', [ListingController::class, 'getVariation'])->name('variations.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Listing Management
    Route::prefix('my-listings')->name('user.listings.')->group(function () {
        Route::get('/', [UserListingController::class, 'index'])->name('index');
        Route::get('/create', [UserListingController::class, 'create'])->name('create');
        Route::post('/', [UserListingController::class, 'store'])->name('store');
        Route::get('/{listing}/edit', [UserListingController::class, 'edit'])->name('edit');
        Route::put('/{listing}', [UserListingController::class, 'update'])->name('update');
        Route::patch('/{listing}/toggle', [UserListingController::class, 'toggleVisibility'])->name('toggle');
        Route::delete('/{listing}', [UserListingController::class, 'destroy'])->name('destroy');
        Route::delete('/{listing}/force', [UserListingController::class, 'forceDestroy'])->name('force-destroy');
        Route::post('/{listing}/reshare', [UserListingController::class, 'reshare'])->name('reshare');
    });
});

require __DIR__.'/auth.php';
