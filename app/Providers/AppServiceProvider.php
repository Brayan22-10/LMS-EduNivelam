<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use App\Models\User; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', function (User $user) {
            return $user->email === 'admin@admin.com';
        });

        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            
            return Limit::perMinute(5)
                ->by($email.'|'.$request->ip());
        });
    }
}