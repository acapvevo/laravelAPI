<?php

namespace App\Http\Controllers\Setting;

use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    use UserTrait;

    public function list()
    {
        $current_user = $this->getCurrentUser(false);
        $users = User::withTrashed()->where('id', '!=', $current_user->id)->get();

        return response()->json($users);
    }

    public function index($id)
    {
        $user = User::with(['address', 'phone_number'])->find($id);

        if (!$user)
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);

        return response()->json([...$user->toArray(), 'roles' => $user->getRoleNames()]);
    }

    public function create(CreateUserRequest $request)
    {
        $request->save();

        return response()->json([
            'type' => 'success',
            'text' => __('User created successfully')
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user)
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);

        $request->save();

        return response()->json([
            'type' => 'success',
            'text' => __('User updated successfully')
        ]);
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user)
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);

        $user->delete();


        return response()->json([
            'type' => 'success',
            'text' => __('User deleted successfully')
        ]);
    }
}
