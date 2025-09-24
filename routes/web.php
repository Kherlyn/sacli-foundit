<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
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

// ============================================================================
// PUBLIC ROUTES - No authentication required
// ============================================================================

// Landing page and public search functionality
Route::get('/', [PublicController::class, 'index'])->name('home');

// Search and browse routes with rate limiting for security
Route::middleware('throttle:search')->group(function () {
    Route::get('/search', [PublicController::class, 'search'])->name('search');
    Route::get('/browse', [PublicController::class, 'browse'])->name('browse');
});

// Individual item display (no rate limiting needed for viewing)
Route::get('/items/{id}', [PublicController::class, 'show'])->name('items.show')->where('id', '[0-9]+');

// ============================================================================
// AUTHENTICATED USER ROUTES - Requires login
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // User dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User profile management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Item management routes
    Route::controller(ItemController::class)->group(function () {
        // Item viewing and listing (no rate limiting)
        Route::get('/items/create', 'create')->name('items.create');
        Route::get('/my-items', 'myItems')->name('items.my-items');
        Route::get('/items/{item}/edit', 'edit')->name('items.edit');
        Route::get('/items/{item}/view', 'show')->name('items.view');

        // Item modification routes with rate limiting for security
        Route::middleware('throttle:submissions')->group(function () {
            Route::post('/items', 'store')->name('items.store');
            Route::patch('/items/{item}', 'update')->name('items.update');
        });

        // Item deletion and image management
        Route::delete('/items/{item}/images/{imageId}', 'removeImage')->name('items.remove-image');
        Route::delete('/items/{item}', 'destroy')->name('items.destroy');
    });
});

// ============================================================================
// ADMIN ROUTES - Requires admin privileges
// ============================================================================

Route::middleware(['auth', 'admin', 'throttle:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->controller(AdminController::class)
    ->group(function () {

        // Dashboard and overview
        Route::get('/dashboard', 'dashboard')->name('dashboard');

        // Item verification and management
        Route::get('/pending-items', 'pendingItems')->name('pending-items');
        Route::post('/items/{item}/verify', 'verify')->name('items.verify');
        Route::post('/items/bulk-action', 'bulkAction')->name('items.bulk-action');
        Route::get('/items', 'items')->name('items');
        Route::get('/items/{item}', 'showItem')->name('items.show');

        // Category management
        Route::get('/categories', 'categories')->name('categories');
        Route::post('/categories', 'storeCategory')->name('categories.store');
        Route::put('/categories/{category}', 'updateCategory')->name('categories.update');
        Route::delete('/categories/{category}', 'deleteCategory')->name('categories.destroy');

        // Statistics and analytics
        Route::get('/statistics', 'statistics')->name('statistics');
        Route::get('/statistics/data', 'statisticsData')->name('statistics.data');
        Route::get('/statistics/export', 'exportStatistics')->name('statistics.export');

        // Admin notifications
        Route::get('/notifications', 'notifications')->name('notifications');
        Route::post('/notifications/mark-read', 'markNotificationsRead')->name('notifications.mark-read');
        Route::post('/notifications/test', 'sendTestNotification')->name('notifications.test');
    });

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

require __DIR__ . '/auth.php';
