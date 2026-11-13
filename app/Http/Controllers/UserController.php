<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:acl_user_index')->only(['index']);
        $this->middleware('permission:acl_user_create')->only(['create', 'store']);
        $this->middleware('permission:acl_user_edit')->only(['edit', 'update']);
        $this->middleware('permission:acl_user_delete')->only(['destroy']);
    }

    public function index()
    {
        $data = User::with('roles')->get();

        return view('v.user.index', compact('data'));
    }

    public function create()
    {
        $roles = $this->assignableRoles();

        return view('v.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,id',
            'password' => 'required',
        ]);

        $role = $this->assignableRole($request->input('role'));

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        $user->syncRoles($role);

        return redirect()->route('user.index');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        $user = $this->editableUser($id);
        $roles = $this->assignableRoles();
        $userRoles = $user->getRoleNames();

        return view('v.user.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, string $id)
    {
        $user = $this->editableUser($id);

        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'required|exists:roles,id',
        ]);

        $role = $this->assignableRole($request->input('role'));

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();
        $user->syncRoles($role);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $this->editableUser($id)->delete();

        return redirect()->route('user.index');
    }

    private function isSuperAdmin(): bool
    {
        return Auth::user()?->hasRole('super-admin') ?? false;
    }

    private function assignableRoles()
    {
        return Role::query()
            ->when(! $this->isSuperAdmin(), fn ($query) => $query->where('name', '!=', 'super-admin'))
            ->orderBy('name')
            ->get();
    }

    private function assignableRole(string $id): Role
    {
        $role = Role::findOrFail($id);
        abort_if($role->name === 'super-admin' && ! $this->isSuperAdmin(), 403);

        return $role;
    }

    // Only a super-admin may change or remove a super-admin account (password reset = takeover).
    private function editableUser(string $id): User
    {
        $user = User::findOrFail($id);
        abort_if($user->hasRole('super-admin') && ! $this->isSuperAdmin(), 403);

        return $user;
    }
}
