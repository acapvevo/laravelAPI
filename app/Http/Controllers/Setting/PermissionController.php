<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function list()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function index(int $id)
    {
        $permission = Permission::find($id);

        if (!$permission)
            return response()->json([
                'type' => 'error',
                'text' => __('Permission not Found')
            ], 400);

        return response()->json($permission);
    }
}
