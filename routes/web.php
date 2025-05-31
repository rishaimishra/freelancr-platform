<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
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

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/jobs', [HomeController::class, 'jobs'])->name('jobs.index');
});

