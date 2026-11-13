<?php

namespace App\Http\Controllers;

use App\Models\User;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:acl_log_index')->only(['index']);
    }

    public function index()
    {
        $allUsers = User::with('roles')->orderByDesc('last_seen')->get();

        return view('v.log.index', compact('allUsers'));
    }
}
