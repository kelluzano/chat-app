<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::get();
        return inertia('SystemSettings/Permissions/Index', compact('permissions'));
    }

    public function create(){
        return inertia('SystemSettings/Permissions/Create');
    }

    public function save(Request $request){
        $validated = $request->validate([
            'name' => ['required', 'min:3', 'unique:permissions,name']
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')->with('message', 'Permission added.');
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        return inertia('SystemSettings/Permissions/Edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'min:3', 'unique:permissions,name']
        ]);

        Permission::where('id', $id)->update($validated);
        return redirect()->route('permissions.index')->with('message', 'Permission has been updated.');
    }

    public function destroy($id)
    {
        Permission::where('id', $id)->delete();
        return redirect()->route('permissions.index')->with('message', 'Permission has been deleted.');;
    }
}
