<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:acl_role_index')->only(['index']);
        $this->middleware('permission:acl_role_show')->only(['show']);
        $this->middleware('permission:acl_role_create')->only(['create', 'store']);
        $this->middleware('permission:acl_role_edit')->only(['edit', 'update']);
        $this->middleware('permission:acl_role_delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when(! $this->isSuperAdmin(), fn ($query) => $query->where('name', '!=', 'super-admin'))
            ->orderBy('name')
            ->get();

        return view('v.role.index', compact('roles'));
    }

    public function create()
    {
        return view('v.role.create', [
            'permissions' => $this->groupedPermissions(),
            'assigned' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permission'] ?? []);

        return redirect()->route('role.index')->with('success', "Hak akses {$role->name} berhasil dibuat.");
    }

    public function show(string $id)
    {
        $role = $this->visibleRole($id);

        return view('v.role.show', [
            'role' => $role,
            'permissions' => $this->groupedPermissions(),
            'assigned' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function edit(string $id)
    {
        $role = $this->visibleRole($id);

        return view('v.role.edit', [
            'role' => $role,
            'permissions' => $this->groupedPermissions(),
            'assigned' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $role = $this->visibleRole($id);
        $data = $this->validated($request, $role);

        // super-admin is matched by name in Gate::before and the installer, so it cannot be renamed.
        if ($role->name !== 'super-admin') {
            $role->update(['name' => $data['name']]);
        }
        $role->syncPermissions($data['permission'] ?? []);

        return redirect()->route('role.index')->with('success', "Hak akses {$role->name} berhasil diperbarui.");
    }

    public function destroy(string $id)
    {
        $role = $this->visibleRole($id);
        abort_if($role->name === 'super-admin', 403, 'Role super-admin tidak dapat dihapus.');

        $role->delete();

        return redirect()->route('role.index')->with('success', "Hak akses {$role->name} berhasil dihapus.");
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:125',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role?->id),
            ],
            'permission' => ['nullable', 'array'],
            'permission.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);
    }

    private function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('super-admin') ?? false;
    }

    private function visibleRole(string $id): Role
    {
        $role = Role::findOrFail($id);
        abort_if($role->name === 'super-admin' && ! $this->isSuperAdmin(), 403);

        return $role;
    }

    /**
     * acl_user_index → ['acl' => ['user' => [Permission, ...]]]; names with two segments land in "general".
     */
    private function groupedPermissions(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => explode('_', $permission->name, 2)[0])
            ->map(fn ($permissions) => $permissions
                ->groupBy(function (Permission $permission) {
                    $parts = explode('_', $permission->name);

                    return count($parts) > 2 ? $parts[1] : 'general';
                })
                ->sortKeys()
                ->all())
            ->sortKeys()
            ->all();
    }
}
