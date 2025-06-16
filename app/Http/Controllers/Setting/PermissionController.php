<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;

/**
 * @group Setting
 *
 * @subgroup Permission
 */
class PermissionController extends Controller
{
    /**
     * Display a listing of the Permission.
     *
     * @queryParam query string A collection of query rules to filter the results. Example: {"combinator":"and","not":false,"rules":[{"field":"name","value":"users.view","operator":"="}]}
     * @queryParam paginate object Pagination options. Example: {"page":1,"size":10}
     * @queryParam paginate.page integer required The page number to retrieve.
     * @queryParam paginate.size integer required The number of items per page.
     * @queryParam sort string A collection of sorting rules. Example: {"id":"created_at","desc":true}
     * @queryParam sort.desc boolean required If true, the results will be sorted in descending order.
     * @queryParam sort.id string required The field by which the results will be sorted.
     *
     * @apiResourceCollection App\Http\Resources\PermissionResource
     *
     * @apiResourceModel App\Models\Permission paginate=10
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $permissions = collect();

        if (isQueryBuilderInRequest($request)) {
            $permissions = modelBuilder($request, Permission::query()->withTrashed());
        } else {
            $permissions = Permission::withTrashed()->get();
        }

        return response()->json(PermissionResource::collection($permissions));
    }

    /**
     * Display the specified Permission.
     *
     * @urlParam id integer required The ID of the Permission to retrieve.
     *
     * @apiResource App\Http\Resources\PermissionResource
     *
     * @apiResourceModel App\Models\Permission
     *
     * @response status=400 {"message": "Permission not found"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $id)
    {
        $permission = Permission::withTrashed()->find($id);

        if (! $permission) {
            return response()->json([
                'type' => 'error',
                'text' => __('Permission not Found'),
            ], 400);
        }

        return response()->json(new PermissionResource($permission));
    }
}
