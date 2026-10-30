<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = \Schema::hasTable('users') ? User::count() : 0;
        $postCount = \Schema::hasTable('posts') ? Post::count() : 0;

        $stats = [
            'users' => $userCount,
            'posts' => $postCount,
        ];

        return view('dashboard', compact('stats'));
    }

    public function security()
    {
        $stats = [
            'users' => \Schema::hasTable('users') ? User::count() : 0,
        ];

        $recentFailedLogins = collect();
        if (\Schema::hasTable('security_logs')) {
            $recentFailedLogins = DB::table('security_logs')
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $failedCount = $recentFailedLogins->count();

        $securityStatus = 'secure'; 
        if ($failedCount > 0 && $failedCount < 5) {
            $securityStatus = 'warning';
        } elseif ($failedCount >= 5) {
            $securityStatus = 'danger';
        }

        return view('admin.security', compact('stats', 'recentFailedLogins', 'failedCount', 'securityStatus'));
    }

    public function clearSystemCache()
    {
        try {
            Artisan::call('optimize:clear');
            return back()->with('success', '¡Caché del sistema y optimizaciones limpiadas y restablecidas con éxito!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al ejecutar el mantenimiento del servidor.');
        }
    }
}