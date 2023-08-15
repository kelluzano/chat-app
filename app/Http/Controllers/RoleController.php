<?php

namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()->with(['permissions' => function ($query) {
            $query->select('name');
        }])->get();
        return inertia('SystemSettings/Roles/Index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::get();
        return inertia('SystemSettings/Roles/Create', compact('permissions'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'min:3', 'unique:roles,name']
        ]);

        $role = Role::create($validated);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions.*.name'));
        }

        return redirect()->route('roles.index')->with('message', 'New Role created.');
    }

    public function edit($id)
    {
        $role = Role::where('id', $id)->with('permissions')->first();
        $permissions = Permission::get();
        return inertia('SystemSettings/Roles/Edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => ['required', 'min:3', Rule::unique('roles', 'name')->ignore($id)],
            'permissions' => ['sometimes', 'array']
        ]);

        $role = Role::where('id', $id)->first();
        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->input('permissions.*.name'));

        return redirect()->route('roles.index')->with('message', 'Role has been updated.');
    }

    public function destroy($id)
    {
        Role::where('id', $id)->delete();
        return redirect()->route('roles.index')->with('message', 'Role has been deleted.');;
    }
}
