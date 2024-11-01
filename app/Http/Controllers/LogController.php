<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeLimit = now()->subMinutes(10);

        $activeUsers = User::where('last_seen', '>=', $timeLimit)->get();
        $activeUsers->transform(function ($user) {
            $user->status = 'Active';
            $user->formatted_last_seen = Carbon::parse($user->last_seen)->diffForHumans();
            return $user;
        });

        $inactiveUsers = User::where('last_seen', '<', $timeLimit)->get();
        $inactiveUsers->transform(function ($user) {
            $user->status = 'Non-active';
            $user->formatted_last_seen = Carbon::parse($user->last_seen)->diffForHumans();
            return $user;
        });

        $allUsers = $activeUsers->merge($inactiveUsers);

        return view('v.log.index', compact('allUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
