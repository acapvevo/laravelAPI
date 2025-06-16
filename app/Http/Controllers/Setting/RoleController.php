<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @group Setting
 *
 * @subgroup Role
 */
class RoleController extends Controller
{
    /**
     * Display a listing of the Role.
     *
     * @queryParam query string A collection of query rules to filter the results. Example: {"combinator":"and","not":false,"rules":[{"field":"name","value":"Super Admin","operator":"="}]}
     * @queryParam paginate object Pagination options. Example: {"page":1,"size":10}
     * @queryParam paginate.page integer required The page number to retrieve.
     * @queryParam paginate.size integer required The number of items per page.
     * @queryParam sort string A collection of sorting rules. Example: {"id":"created_at","desc":true}
     * @queryParam sort.desc boolean required If true, the results will be sorted in descending order.
     * @queryParam sort.id string required The field by which the results will be sorted.
     *
     * @apiResourceCollection App\Http\Resources\RoleResource
     *
     * @apiResourceModel App\Models\Role paginate=10
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $roles = collect();

        if (isQueryBuilderInRequest($request)) {
            $roles = modelBuilder($request, Role::query()->withTrashed());
        } else {
            $roles = Role::withTrashed()->get();
        }

        return response()->json(RoleResource::collection($roles));
    }

    /**
     * Display the specified Role.
     *
     * @urlParam id integer required The ID of the Role to retrieve.
     *
     * @apiResource App\Http\Resources\RoleResource
     *
     * @apiResourceModel App\Models\Role
     *
     * @response status=400 {"message": "Role not found"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $id)
    {
        $role = Role::find($id);

        if (! $role) {
            return response()->json([
                'type' => 'error',
                'text' => __('Role not Found'),
            ], 400);
        }

        return response()->json(new RoleResource($role));
    }

    /**
     * Create a new Role.
     *
     * @bodyParam name string required The name of the role. Example: Admin
     * @bodyParam permissions array required The permissions associated with the role. Example: ["users.view", "users.edit"]
     * @bodyParam permissions.* string The name of the permission. Example: users.view
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name|distinct',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->noContent();
    }

    /**
     * Update the specified Role.
     *
     * @urlParam id integer required The ID of the Role to update.
     *
     * @bodyParam name string required The name of the role. Example: Admin
     * @bodyParam permissions array required The permissions associated with the role. Example: ["users.view", "users.edit"]
     * @bodyParam permissions.* string The name of the permission. Example: users.view
     *
     * @response status=400 {"message": "Role not found"}
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $role = Role::find($id);

        if (! $role) {
            return response()->json([
                'type' => 'error',
                'text' => __('Role not Found'),
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,'.$id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name|distinct',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->noContent();
    }

    /**
     * Toggle Availbility the specified Role.
     *
     * @urlParam id integer required The ID of the Role to delete.
     *
     * @response status=400 {"message": "Role not found"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $id)
    {
        $role = Role::withTrashed()->find($id);

        if (! $role) {
            return response()->json([
                'type' => 'error',
                'text' => __('Role not Found'),
            ], 400);
        }

        if ($id == 2 || $id == 1) {
            return response()->json([
                'type' => 'warning',
                'text' => __('You cannot delete Super Admin and Default role'),
            ], 400);
        }

        if ($role->trashed()) {
            $role->restore();
        } else {
            $role->delete();
        }

        return response()->noContent();
    }
}
