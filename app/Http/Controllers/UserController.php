<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
     public function index()
    {
       $data=User::all();
       return view('v.user.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::get();
        return view('v.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required|exists:roles,id',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        $roleName = Role::find($request['role'])->name;
        $user->assignRole($roleName);
        $this->logActivity('create', 'Created a new user: ' . $request->name);
        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $user = User::find($id);
        $roles = Role::all(); // Mengambil semua role
        $userRoles = $user->getRoleNames(); // Mendapatkan nama role yang dimiliki user

        return view('v.user.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        // Temukan user berdasarkan ID
        $user = User::find($id);

        // Update data user
        $user->name = $request->name;
        $user->email = $request->email;

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Hapus role lama
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        // Ambil nama role baru dan assign
        $roleName = Role::find($request['role'])->name;
        $user->assignRole($roleName);

        // Simpan perubahan
        $user->save();

        // Log aktivitas
        $this->logActivity('edit', 'Updated user: ' . $request->name);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        $this->logActivity('delete', 'Deleted user with User: ' . $user->name);
        return redirect()->route('user.index');
    }
}
