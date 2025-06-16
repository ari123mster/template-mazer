<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
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


        // return view('v.role.create', compact('permissions'));
        $permissions = Permission::all();

        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            // Misal nama permission: acl_user_index
            $parts = explode('_', $permission->name, 3);

            // Ambil parent = acl_user (gabungan 2 kata pertama), child = sisanya
            if (count($parts) >= 3) {
                $parent = $parts[0] . '_' . $parts[1]; // acl_user
                $child = $parts[2];                   // index, create, dll
            } else {
                // Jika hanya ada 2 bagian, parent = permission itu sendiri, child kosong
                $parent = $permission->name;
                $child = null;
            }

            // Simpan ke grup
            if (!isset($groupedPermissions[$parent])) {
                $groupedPermissions[$parent] = [];
            }

            $groupedPermissions[$parent][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'child_label' => $child,
            ];
        }

        return view('v.role.create', ['permissions' => $groupedPermissions]);
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
        // dd($role);  $this->logActivity('create', 'Created a new role: ' . $role->name . ' with permissions: ' . implode(', ', $request->input('permission')));
        return redirect()->route('role.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


    // return view('v.role.show', compact('roles', 'permission', 'rolePermissions', 'permissionsData'));
    $roles = Role::findOrFail($id);

    // Ambil semua permission yang mengandung 'acl' di nama-nya
    $allPermissions = Permission::where('name', 'LIKE', 'acl%')->get();

    // Ambil permission yang sudah ditugaskan ke role tersebut
    $assignedPermissions = DB::table('role_has_permissions')
        ->where('role_id', $id)
        ->pluck('permission_id')
        ->toArray();

    // Kelompokkan permission berdasarkan parent prefix (sebelum underscore kedua)
    // Misal acl_user_index => acl_user jadi kategori parent
    $permissionsData = [];

    foreach ($allPermissions as $perm) {
        // contoh: acl_user_index -> ['acl', 'user', 'index']
        $parts = explode('_', $perm->name);

        // ambil parent sebagai dua kata pertama: acl_user
        $parent = isset($parts[0]) && isset($parts[1]) ? $parts[0] . '_' . $parts[1] : $parts[0];

        // Simpan permission ke dalam parent group
        if (!isset($permissionsData[$parent])) {
            $permissionsData[$parent] = [
                'all' => [],
                'assigned' => [],
            ];
        }

        // Masukkan ke 'all'
        $permissionsData[$parent]['all'][] = $perm;

        // Jika permission sudah assigned, masukin juga ke assigned
        if (in_array($perm->id, $assignedPermissions)) {
            $permissionsData[$parent]['assigned'][] = $perm;
        }
    }

    // Kirim data ke view
    return view('v.role.show', compact('roles', 'permissionsData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);
        $allPermissions = Permission::all();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_id', $id)
            ->pluck('permission_id')
            ->toArray();

        $groupedPermissions = [];

        foreach ($allPermissions as $permission) {
            $parts = explode('_', $permission->name, 3);

            if (count($parts) >= 3) {
                $parent = $parts[0] . '_' . $parts[1];
                $child = $parts[2];
            } else {
                $parent = $permission->name;
                $child = null;
            }

            if (!isset($groupedPermissions[$parent])) {
                $groupedPermissions[$parent] = [];
            }

            $groupedPermissions[$parent][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'child_label' => $child,
            ];
        }

        return view('v.role.edit', [
            'roles' => $role,
            'permissions' => $groupedPermissions,
            'rolePermissions' => $rolePermissions
        ]);
    //     $roles = Role::find($id);
    // $permission = Permission::get();
    // $rolePermissions = DB::table('role_has_permissions')
    //     ->where('role_id', $id)
    //     ->pluck('permission_id', 'permission_id')
    //     ->all();

    // // Daftar nama permission yang ingin dicari
    // $permissionNames = [
    //     'acl'
    // ];

    // // Mengambil permissions yang sesuai dengan nama
    // $permissions = [];
    // foreach ($permissionNames as $name) {
    //     $permissions[$name] = [
    //         'all' => DB::table('permissions')
    //             ->where('permissions.name', 'LIKE', "%$name%")
    //             ->select('permissions.name')
    //             ->get(),
    //         'assigned' => Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
    //             ->where('role_has_permissions.role_id', $id)
    //             ->where('permissions.name', 'LIKE', "%$name%")
    //             ->get()
    //     ];
    // }

    // return view('v.role.edit', compact('roles', 'permission', 'rolePermissions', 'permissions'));
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
        $oldName = $role->name;
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
