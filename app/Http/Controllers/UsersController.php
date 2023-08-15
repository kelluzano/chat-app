<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function index(){

        $users = User::query()
                ->select('id', 'name')
                ->paginate(10)
                ->withQueryString();

        return inertia('SystemSettings/Users/Index', compact('users'));
    }

    public function create(){

    }

    public function edit($id){
        $user = User::with('roles')->find($id);
        $roles = Role::get();
        return inertia('SystemSettings/Users/Edit', compact('user', 'roles'));
    }

    public function update($id, Request $request){
        $validated = $request->validate([
            'name' => ['required', 'min:5'],
            'email' => ['required', 'email']
        ]);

        $user = User::find($id);
        $user->update($validated);
        $user->syncRoles($request->input('roles.*.name'));

        return redirect()->route('users.index');
    }

    public function destroy(User $user){
        $user->delete();
        return redirect()->back()->with(['message' => 'success', 'class' => 'alert-danger']);
    }
}
