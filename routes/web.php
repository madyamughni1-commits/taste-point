<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

// PERBAIKAN: Redirect root ke route login (bukan langsung view)
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================
// ROUTE UNTUK GUEST (BELUM LOGIN)
// ============================

// Halaman login & register
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ============================
// ROUTE UNTUK USER YANG SUDAH LOGIN
// ============================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Home / Dashboard User
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Pencarian & Detail Restoran
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    Route::get('/restaurant/{id}', [HomeController::class, 'restaurantDetail'])->name('restaurant.detail');
    
    // Deteksi Makanan (NEW)
    Route::post('/detect-food', [HomeController::class, 'detectFood'])->name('detect.food');
    
    // Update Location (NEW)
    Route::post('/update-location', [HomeController::class, 'updateLocation'])->name('update.location');
    
    // Budget Search (NEW)
    Route::post('/search-budget', [HomeController::class, 'searchByBudget'])->name('search.budget');
});

// ============================
// ROUTE UNTUK ADMIN
// ============================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Manajemen Restoran
    Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/create', [AdminController::class, 'createRestaurant'])->name('restaurants.create');
    Route::post('/restaurants', [AdminController::class, 'storeRestaurant'])->name('restaurants.store');
    Route::get('/restaurants/{id}/edit', [AdminController::class, 'editRestaurant'])->name('restaurants.edit');
    Route::put('/restaurants/{id}', [AdminController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::delete('/restaurants/{id}', [AdminController::class, 'deleteRestaurant'])->name('restaurants.delete');
    
    // Delete restaurant image
    Route::delete('/restaurants/{id}/image', [AdminController::class, 'deleteRestaurantImage'])->name('restaurants.deleteImage');
    
    // Manajemen Menu
    Route::get('/menus/create', [AdminController::class, 'createMenu'])->name('menus.create');
    Route::get('/restaurants/{restaurantId}/menus', [AdminController::class, 'menus'])->name('menus');
    Route::post('/menus', [AdminController::class, 'storeMenu'])->name('menus.store');
    Route::put('/menus/{id}', [AdminController::class, 'updateMenu'])->name('menus.update');
    Route::delete('/menus/{id}', [AdminController::class, 'deleteMenu'])->name('menus.delete');
    
    // All Menus
    Route::get('/all-menus', [AdminController::class, 'allMenus'])->name('all-menus');
    
    // Bulk Menu Upload (NEW)
    Route::get('/bulk-menu-upload', [AdminController::class, 'bulkMenuUpload'])->name('bulk-menu-upload');
    Route::post('/bulk-menu-upload', [AdminController::class, 'processBulkMenuUpload'])->name('bulk-menu-upload.process');
    Route::get('/bulk-menu-review', [AdminController::class, 'reviewBulkMenus'])->name('bulk-menu-review');
    Route::post('/bulk-menu-save', [AdminController::class, 'saveBulkMenus'])->name('bulk-menu-save');
    Route::post('/bulk-menu-cancel', [AdminController::class, 'cancelBulkMenus'])->name('bulk-menu-cancel');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Reviews Management
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview'])->name('reviews.delete');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    
    // Activity Logs
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    Route::delete('/activity-logs/{id}', [AdminController::class, 'deleteActivityLog'])->name('activity-logs.delete');
    Route::post('/activity-logs/clear', [AdminController::class, 'clearActivityLogs'])->name('activity-logs.clear');
});