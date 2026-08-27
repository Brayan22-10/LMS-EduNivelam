<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IncidentController; 

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/reportes', [SiteController::class, 'reports'])->name('site.reports');
Route::get('/directorio', [SiteController::class, 'directory'])->name('site.directory');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/panel', [PageController::class, 'dashboard'])->name('dashboard');
});

Route::resource('incidentes', IncidentController::class);

