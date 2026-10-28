<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest; 
use App\Http\Controllers\PageController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\ReceivedEmailController;
use App\Http\Controllers\SettingsController; 
use App\Http\Controllers\NewsController; 
use Illuminate\Support\Facades\Schedule;

Schedule::command('news:publish-scheduled')->everyMinute();
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

    Route::middleware('throttle:10,1')->group(function () {
        Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
            ->name('auth.google.redirect');
        Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
            ->name('auth.google.callback');
    });

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/register/teacher', [AuthController::class, 'showTeacherRegister'])->name('register.teacher');
    Route::post('/register/teacher', [AuthController::class, 'registerTeacher'])->name('register.teacher.store');
    
    Route::get('/teacher/pending-approval', function () {
        return view('auth.teacher-pending');
    })->name('teacher.pending');

    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('/verify-code', [AuthController::class, 'showVerifyForm'])->name('password.verify.form');
    Route::post('/verify-code', [AuthController::class, 'verifyCode'])
        ->middleware('throttle:5,1')
        ->name('password.verify.submit');

    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/session-expired', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->view('errors.419', [], 419);
    })->name('session.expired');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/check-session-status', function (Request $request) {
        $user = $request->user();
        if ($user && (!is_null($user->email_verified_at) || ($user->is_active ?? false))) {
            return response()->json(['status' => 'verified']);
        }
        return response()->json(['status' => 'pending']);
    })->name('verification.status-check');

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
    Route::get('/admin/profile', [SettingsController::class, 'profileView'])->name('admin.profile');
    Route::put('/admin/profile', [SettingsController::class, 'updateProfile'])->name('admin.profile.update');

    Route::middleware(['maintenance'])->group(function () {
        Route::get('/student/dashboard', function () {
            return view('student.dashboard');
        })->name('student.dashboard');

        Route::get('/teacher/dashboard', function () {
            return view('teacher.dashboard');
        })->name('teacher.dashboard');
    });
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

    Route::put('/admin/users/{id}/role', [UserController::class, 'updateRole'])
        ->name('admin.users.updateRole')
        ->middleware(['can:manage-users']);

    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy')
        ->middleware(['can:manage-users']);

    Route::delete('/admin/users/permanent/{id}', [UserController::class, 'forceDelete'])
        ->name('admin.users.forceDelete')
        ->middleware(['can:manage-users']);
});