<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $onlineSince = now()->subMinutes(10);

        return view('v.dashboard.index', [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'onlineUsers' => User::with('roles')->where('last_seen', '>=', $onlineSince)->orderByDesc('last_seen')->get(),
            'recentLogs' => $user->can('acl_log_index')
                ? ActivityLog::with('user')->latest()->limit(8)->get()
                : collect(),
        ]);
    }
}
