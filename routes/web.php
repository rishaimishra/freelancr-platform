<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CecaPaymentController;
use App\Http\Controllers\PayPalPaymentController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PusherBroadcastController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Home Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register/freelancer', [AuthController::class, 'showFreelancerForm'])->name('register.freelancer');
Route::get('/register/client', [AuthController::class, 'showClientForm'])->name('register.client');
Route::post('/register/freelancer', [AuthController::class, 'registerFreelancer']);
Route::post('/register/client', [AuthController::class, 'registerClient']);
Route::get('/provincias/{pais_id}', [ProvinciaController::class, 'getByPais']);

// Public Job Routes
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/freelancer-jobs/{job}/edit', [JobController::class, 'freelanceredit'])->name('freelance.jobs.edit');

    // Job Management Routes (for clients)
    Route::middleware(['role:user'])->group(function () {
        Route::get('/jobs-create', [JobController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
        Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
        Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
        Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
    });

    // Job Application Routes (for contractors)
    Route::middleware(['role:contractor'])->group(function () {
        Route::get('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');
      
    });

    // Chat Routes
    Route::get('/messages/{job}', [PusherBroadcastController::class, 'index'])->name('messages.index');
    Route::post('/messages/{job}/send', [PusherBroadcastController::class, 'broadcast'])->name('messages.broadcast');
    Route::post('/messages/{job}/receive', [PusherBroadcastController::class, 'receive'])->name('messages.receive');
});

// Payment routes
Route::get('/payment/paypal/{payment}', [PayPalPaymentController::class, 'handlePayment'])
    ->name('payment.paypal');
Route::get('/payment/success/{payment}', [PayPalPaymentController::class, 'paymentSuccess'])
    ->name('payment.success');
Route::get('/payment/cancel/{payment}', [PayPalPaymentController::class, 'paymentCancel'])
    ->name('payment.cancel');
Route::get('/payment/invoice/{payment}', [PayPalPaymentController::class, 'downloadInvoice'])
    ->name('payment.invoice');

// CECA Payment Routes
Route::get('/jobs/{job}/pay/credit-card', [CecaPaymentController::class, 'showPaymentForm'])
    ->name('payment.ceca.form');
Route::post('/payment/ceca/success/{payment}', [CecaPaymentController::class, 'handleSuccess'])
    ->name('payment.ceca.success');
Route::post('/payment/ceca/failed/{payment}', [CecaPaymentController::class, 'handleFailure'])
    ->name('payment.ceca.failed');

Route::post('/generate-hash', [CecaPaymentController::class, 'generateHash'])->name('generate.hash');


// Admin Login
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

// Admin Routes
// Route::middleware(['role:admin'])->prefix('admin')->group(function () {
//     // Admin Dashboard
//     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

//     // User Management
//     Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
//     Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
//     Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
//     Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
//     Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

//     // Job Management
//     Route::get('/jobs', [JobController::class, 'adminIndex'])->name('admin.jobs.index');
//     Route::get('/jobs/{job}', [JobController::class, 'adminShow'])->name('admin.jobs.show');
//     Route::delete('/jobs/{job}', [JobController::class, 'adminDestroy'])->name('admin.jobs.destroy');
// });

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs');

    Route::get('/jobs/{job}/edit', [AdminController::class, 'editJob'])->name('jobs.edit');

    Route::put('/jobs/{job}', [AdminController::class, 'updateJob'])->name('jobs.update');
    Route::put('/jobs/{job}/status', [AdminController::class, 'updateJobStatus'])->name('jobs.update.status');
    Route::delete('/jobs/{job}', [AdminController::class, 'destroyJob'])->name('jobs.destroy');
    Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::get('/stats/users', [AdminController::class, 'getUserStats'])->name('stats.users');

    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});