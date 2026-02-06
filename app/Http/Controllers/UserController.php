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
    public function __construct()
    {
        $this->middleware('permission:acl_user_index')->only(['index']);
        $this->middleware('permission:acl_user_create')->only(['create', 'store']);
        $this->middleware('permission:acl_user_edit')->only(['edit', 'update']);
        $this->middleware('permission:acl_user_delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */

    //  protected function logActivity($action, $description)
    // {
    //     ActivityLog::create([
    //         'user_id' => Auth::id(),
    //         'action' => $action,
    //         'description' => $description,
    //     ]);
    // }
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
        $roles = Role::query()
            ->when(!Auth::user()->hasRole('super-admin'), function ($q) {
                $q->where('name', '!=', 'super-admin');
            })
            ->get();
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

        $selectedRole = Role::find($request['role']);
        if ($selectedRole && $selectedRole->name === 'super-admin' && !Auth::user()->hasRole('super-admin')) {
            abort(403);
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        $user->assignRole($selectedRole->name);
        // $this->logActivity('create', 'Created a new user: ' . $request->name);
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
        $roles = Role::query()
            ->when(!Auth::user()->hasRole('super-admin'), function ($q) {
                $q->where('name', '!=', 'super-admin');
            })
            ->get(); // Mengambil role sesuai akses
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

        $selectedRole = Role::find($request['role']);
        if ($selectedRole && $selectedRole->name === 'super-admin' && !Auth::user()->hasRole('super-admin')) {
            abort(403);
        }

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
        $user->assignRole($selectedRole->name);

        // Simpan perubahan
        $user->save();

        // Log aktivitas
        // $this->logActivity('edit', 'Updated user: ' . $request->name);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        // $this->logActivity('delete', 'Deleted user with User: ' . $user->name);
        return redirect()->route('user.index');
    }
}
