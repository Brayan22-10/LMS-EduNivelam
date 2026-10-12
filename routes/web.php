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
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\ReceivedEmailController;
use App\Http\Controllers\SettingsController; 
use App\Http\Controllers\NewsController; 

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/instructors', [SiteController::class, 'directory'])->name('instructors');
Route::get('/courses', [SiteController::class, 'reports'])->name('courses');

Route::get('/news', [NewsController::class, 'publicIndex'])->name('news.index');

Route::resource('incidentes', IncidentController::class);
Route::resource('posts', PostController::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/register/teacher', [AuthController::class, 'showTeacherRegister'])->name('register.teacher');
    Route::post('/register/teacher', [AuthController::class, 'registerTeacher'])->name('register.teacher.store');
    
    Route::get('/teacher/pending-approval', function () {
        return view('auth.teacher-pending');
    })->name('teacher.pending');

    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');

    Route::get('/verify-code', [AuthController::class, 'showVerifyForm'])->name('password.verify.form');
    Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('password.verify.submit');

    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/check-session-status', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['status' => 'verified']);
        }
        return response()->json(['status' => 'pending'], 403);
    });

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return view('auth.verified-success');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '¡Se ha enviado un nuevo enlace de verificación a tu correo!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');
    Route::post('/settings/toggle-theme', [SettingsController::class, 'toggleTheme'])->name('settings.toggle-theme');
    Route::get('/settings/download-data', [SettingsController::class, 'downloadPersonalData'])->name('settings.data.download');
    Route::post('/settings/feedback', [SettingsController::class, 'sendFeedback'])->name('settings.feedback.send');

    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');

    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->name('teacher.dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/check-imap', [ContactController::class, 'validateImapConnection'])
        ->name('admin.check.imap');
    
    Route::get('/admin/correos', [ReceivedEmailController::class, 'index'])->name('admin.emails.index');
    Route::get('/admin/correos/{id}', [ReceivedEmailController::class, 'show'])->name('admin.emails.show');
    Route::delete('/admin/correos/{id}', [ReceivedEmailController::class, 'destroy'])->name('admin.emails.destroy');
    Route::post('/admin/correos/{id}/reply', [ReceivedEmailController::class, 'reply'])->name('admin.emails.reply');

    Route::get('/admin/teachers/pending', [AdminTeacherController::class, 'index'])->name('admin.teachers.pending');
    Route::post('/admin/teachers/{id}/approve', [AdminTeacherController::class, 'approve'])->name('admin.teachers.approve');
    Route::delete('/admin/teachers/{id}/deny', [AdminTeacherController::class, 'deny'])->name('admin.teachers.deny');

    Route::get('/admin/news', [NewsController::class, 'index'])
        ->name('admin.news.index')
        ->middleware(['can:manage-users']);
        
    Route::get('/admin/news/create', [NewsController::class, 'create'])
        ->name('admin.news.create')
        ->middleware(['can:manage-users']);
        
    Route::post('/admin/news', [NewsController::class, 'store'])
        ->name('admin.news.store')
        ->middleware(['can:manage-users']);

    Route::get('/admin/news/{news}', [NewsController::class, 'show'])
        ->name('admin.news.show')
        ->middleware(['can:manage-users']);

    Route::get('/admin/news/{news}/edit', [NewsController::class, 'edit'])
        ->name('admin.news.edit')
        ->middleware(['can:manage-users']);

    Route::put('/admin/news/{news}', [NewsController::class, 'update'])
        ->name('admin.news.update')
        ->middleware(['can:manage-users']);

    Route::delete('/admin/news/{news}', [NewsController::class, 'destroy'])
        ->name('admin.news.destroy')
        ->middleware(['can:manage-users']);

    Route::resource('/admin/banners', BannerController::class)
        ->names('admin.banners')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/maintenance', [SettingsController::class, 'toggleMaintenance'])
        ->name('admin.system.maintenance')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/test-email', [SettingsController::class, 'testEmailConnection'])
        ->name('admin.system.testemail')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/backup', [SettingsController::class, 'generateBackup'])
        ->name('admin.system.backup')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/clear-cache', [SettingsController::class, 'clearSystemCache'] ?? [DashboardController::class, 'clearSystemCache'])
        ->name('admin.system.clearcache')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/logs/view', [SettingsController::class, 'viewLogs'])
        ->name('admin.system.logs.view')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/logs/clear', [SettingsController::class, 'clearLogs'])
        ->name('admin.system.logs.clear')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/smtp/test', [SettingsController::class, 'testSmtp'])
        ->name('admin.system.smtp.test')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/db/optimize', [SettingsController::class, 'optimizeDatabase'])
        ->name('admin.system.db.optimize')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/cache/inspect', [SettingsController::class, 'inspectCache'])
        ->name('admin.system.cache.inspect')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/cache/granular', [SettingsController::class, 'clearGranularCache'])
        ->name('admin.system.cache.granular')
        ->middleware(['can:manage-users']);

    Route::post('/admin/system/env/update', [SettingsController::class, 'updateEnv'])
        ->name('admin.system.env.update')
        ->middleware(['can:manage-users']);

    Route::get('/admin/system/config', function() {
        return redirect()->route('settings.index');
    })->name('admin.system.config')->middleware(['can:manage-users']);

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(['can:manage-users']); 
    
    Route::get('/admin/security', [DashboardController::class, 'security'])
        ->name('admin.security')
        ->middleware(['can:manage-users']);

    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users')
        ->middleware(['can:manage-users']); 

    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy')
        ->middleware(['can:manage-users']);

    Route::delete('/admin/users/permanent/{id}', [UserController::class, 'forceDelete'])
        ->name('admin.users.forceDelete')
        ->middleware(['can:manage-users']);
});