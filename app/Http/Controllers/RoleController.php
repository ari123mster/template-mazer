<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = role::get();

        return view('v.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissionNames = [
            'acl'
        ];

        // Mengambil permissions yang sesuai dengan nama
        $permissions = [];
        foreach ($permissionNames as $name) {
            $permissions[$name] = DB::table('permissions')
                ->where('permissions.name', 'LIKE', "%$name%")
                ->select('permissions.name')
                ->get();
        }

        return view('v.role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        // dd($role);

        return redirect()->route('role.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roles = Role::find($id);
    $permission = Permission::all();
    $rolePermissions = DB::table('role_has_permissions')->where('role_id', $id)
        ->pluck('permission_id', 'permission_id')
        ->all();

    // Daftar nama permission yang ingin dicari
    $permissionNames = [
        'acl'
    ];

    // Mengambil permissions yang sesuai dengan nama
    $permissionsData = [];
    foreach ($permissionNames as $name) {
        $permissionsData[$name] = [
            'all' => DB::table('permissions')
                ->where('permissions.name', 'LIKE', "%$name%")
                ->select('permissions.name')
                ->get(),
            'assigned' => Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_has_permissions.role_id', $id)
                ->where('permissions.name', 'LIKE', "%$name%")
                ->get()
        ];
    }

    return view('v.role.show', compact('roles', 'permission', 'rolePermissions', 'permissionsData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = Role::find($id);
    $permission = Permission::get();
    $rolePermissions = DB::table('role_has_permissions')
        ->where('role_id', $id)
        ->pluck('permission_id', 'permission_id')
        ->all();

    // Daftar nama permission yang ingin dicari
    $permissionNames = [
        'acl'
    ];

    // Mengambil permissions yang sesuai dengan nama
    $permissions = [];
    foreach ($permissionNames as $name) {
        $permissions[$name] = [
            'all' => DB::table('permissions')
                ->where('permissions.name', 'LIKE', "%$name%")
                ->select('permissions.name')
                ->get(),
            'assigned' => Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_has_permissions.role_id', $id)
                ->where('permissions.name', 'LIKE', "%$name%")
                ->get()
        ];
    }

    return view('v.role.edit', compact('roles', 'permission', 'rolePermissions', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('role.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roles = Role::find($id);
        $roles->delete();

        return redirect()->route('role.index');
    }
}
