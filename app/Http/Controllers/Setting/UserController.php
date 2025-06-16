<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

/**
 * @group Setting
 *
 * @subgroup User
 */
class UserController extends Controller
{
    use UserTrait;

    /**
     * Display a listing of the User.
     *
     * @queryParam query string A collection of query rules to filter the results. Example: {"combinator":"and","not":false,"rules":[{"field":"name","value":"John Doe","operator":"="}]}
     * @queryParam paginate object Pagination options. Example: {"page":1,"size":10}
     * @queryParam paginate.page integer required The page number to retrieve.
     * @queryParam paginate.size integer required The number of items per page.
     * @queryParam sort string A collection of sorting rules. Example: {"id":"created_at","desc":true}
     * @queryParam sort.desc boolean required If true, the results will be sorted in descending order.
     * @queryParam sort.id string required The field by which the results will be sorted.
     *
     * @apiResourceCollection App\Http\Resources\UserResource
     *
     * @apiResourceModel App\Models\User paginate=10
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $users = collect();

        if (isQueryBuilderInRequest($request)) {
            $current_user = $this->getCurrentUser(false);

            $users = modelBuilder($request, User::query()->withTrashed()->where('id', '!=', $current_user->id));
        } else {
            $users = User::withTrashed()->get();
        }

        return response()->json(UserResource::collection($users));
    }

    /**
     * Display the specified User.
     *
     * @urlParam int id The ID of the user to retrieve.
     *
     * @apiResource App\Http\Resources\UserResource
     *
     * @apiResourceModel App\Models\User
     *
     * @response status=400 {"message": "User not found"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        $user = User::withTrashed()->find($id);

        if (! $user) {
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);
        }

        return response()->json(new UserResource($user));
    }

    /**
     * Create a new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateUserRequest $request)
    {
        $request->save();

        return response()->noContent();
    }

    /**
     * Update the specified User.
     *
     * @urlParam int id The ID of the user to update.
     *
     * @response status=400 {"message": "User not found"}
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::withTrashed()->find($id);

        if (! $user) {
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);
        }

        $request->save();

        return response()->noContent();
    }

    /**
     * Toggle Availbility the specified User.
     *
     * @urlParam int id The ID of the user to toggle its availbility.
     *
     * @response status=400 {"message": "User not found"}
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = User::withTrashed()->find($id);

        if (! $user) {
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);
        }

        if ($user->trashed()) {
            $user->restore();
        } else {
            $user->delete();
        }

        return response()->noContent();
    }
}
