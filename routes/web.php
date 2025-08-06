<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes
// Public Routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
Route::get('/dressup', [ProductController::class, 'dressup'])->name('dressup');
Route::get('/products/{category}', [ProductController::class, 'category'])->name('products.category')->where('category', '[0-9]+');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add')->middleware('auth');
Route::post('/cart/bulk-add', [CartController::class, 'bulkAdd'])->name('cart.bulk-add')->middleware('auth');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('auth');
Route::post('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove')->middleware('auth');

// API-like Routes for Dress Up
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
Route::get('/subcategories/{subcategory}/products', [ProductController::class, 'getProductsBySubcategory'])->name('subcategories.products');

// Admin Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('auth');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

// User Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
    Route::get('/user/edit', [DashboardController::class, 'editProfile'])->name('user.edit');
    Route::put('/user', [DashboardController::class, 'updateProfile'])->name('user.update');
});