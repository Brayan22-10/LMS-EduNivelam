<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest; 
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

    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');

    Route::get('/verify-code', [AuthController::class, 'showVerifyForm'])->name('password.verify.form');
    Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('password.verify.submit');

    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
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

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', '¡Correo verificado con éxito!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '¡Se ha enviado un nuevo enlace de verificación a tu correo!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(['can:manage-users', 'verified']); 
    
    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users')
        ->middleware(['can:manage-users', 'verified']); 
});