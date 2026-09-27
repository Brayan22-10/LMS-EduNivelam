<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'posts' => Post::count(),
        ];

        return view('dashboard', compact('stats'));
    }

    public function security()
    {
        $stats = [
            'users' => User::count(),
        ];

        $recentFailedLogins = DB::table('security_logs')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get();

        $failedCount = $recentFailedLogins->count();

        $securityStatus = 'secure'; 
        if ($failedCount > 0 && $failedCount < 5) {
            $securityStatus = 'warning';
        } elseif ($failedCount >= 5) {
            $securityStatus = 'danger';
        }

        return view('admin.security', compact('stats', 'recentFailedLogins', 'failedCount', 'securityStatus'));
    }
}