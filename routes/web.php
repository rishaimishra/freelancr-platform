<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;
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
        Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');
    });
});

