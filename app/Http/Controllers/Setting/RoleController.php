<?php

namespace App\Http\Controllers\Setting;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function list()
    {
        $roles = Role::withTrashed()->get();
        return response()->json($roles);
    }

    public function index(int $id)
    {
        $role = Role::find($id);

        if (!$role)
            return response()->json([
                'type' => 'error',
                'text' => __('Role not Found')
            ], 400);

        return response()->json([...$role->toArray(), 'permissions' => $role->permissions->pluck('name')]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name|distinct'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'type' => 'success',
            'text' => __('Role created successfully')
        ]);
    }

    public function update(int $id, Request $request)
    {
        $role = Role::find($id);

        if (!$role)
            return response()->json([
                'type' => 'error',
                'text' => __('Role not Found')
            ], 400);

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name|distinct'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'type' => 'success',
            'text' => __('Role updated successfully')
        ]);
    }

    public function delete(int $id)
    {
        $role = Role::find($id);

        if (!$role)
            return response()->json([
                'type' => 'error',
                'text' => __('Role not Found')
            ], 400);

        if ($id == 2 || $id == 1)
            return response()->json([
                'type' => 'error',
                'text' => __('You cannot delete Super Admin and Default role')
            ], 400);

        $role->delete();

        return response()->json([
            'type' => 'success',
            'text' => __('Role deleted successfully')
        ]);
    }
}
