<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenanceMode = Cache::get('system_maintenance', false);

        if ($isMaintenanceMode) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'El sistema se encuentra en mantenimiento temporalmente.',
                ], 503);
            }

            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
