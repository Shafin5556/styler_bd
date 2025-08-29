<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email');
Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp'])->name('password.verify.otp');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

// Public Routes
// Public Routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
Route::get('/dressup', [ProductController::class, 'dressup'])->name('dressup');
Route::get('/products/{category}', [ProductController::class, 'category'])->name('products.category')->where('category', '[0-9]+');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add')->middleware('auth');
Route::post('/cart/bulk-add', [CartController::class, 'bulkAdd'])->name('cart.bulk-add')->middleware('auth');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('auth');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove')->middleware('auth');

// API-like Routes for Dress Up
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
Route::get('/subcategories/{subcategory}/products', [ProductController::class, 'getProductsBySubcategory'])->name('subcategories.products');


// Checkout and Order Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // SSLCommerz Callback Routes (POST as per repo)
    Route::post('/payment/success', [OrderController::class, 'paymentSuccess'])->name('payment.success');
    Route::post('/payment/fail', [OrderController::class, 'paymentFail'])->name('payment.fail');
    Route::post('/payment/cancel', [OrderController::class, 'paymentCancel'])->name('payment.cancel');
    Route::post('/payment/ipn', [OrderController::class, 'paymentIpn'])->name('payment.ipn');
    Route::post('/payment/pay-via-ajax', [OrderController::class, 'payViaAjax'])->name('payment.pay-via-ajax'); // Optional for popup
});

// Admin Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::post('/admin/orders/update-status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
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
Route::get('/pay-pending-orders', [OrderController::class, 'payPendingOrders'])->name('pay-pending-orders');
});