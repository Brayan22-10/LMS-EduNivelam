<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ReceivedEmailController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/instructors', [SiteController::class, 'directory'])->name('instructors');
Route::get('/courses', [SiteController::class, 'reports'])->name('courses');

Route::resource('incidentes', IncidentController::class);
Route::resource('posts', PostController::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::post('/check-session-status', function (Request $request) {
        return response()->json(['status' => 'active']);
    });
    
    Route::get('/admin/check-imap', [ContactController::class, 'validateImapConnection'])
        ->name('admin.check.imap');
    
    Route::get('/admin/correos', [ReceivedEmailController::class, 'index'])->name('admin.emails.index');
    Route::get('/admin/correos/{id}', [ReceivedEmailController::class, 'show'])->name('admin.emails.show');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('can:manage-users');
    
    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users')
        ->middleware('can:manage-users'); 
});