<?php

namespace App\Http\Controllers\Setting;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index($id)
    {
        $user = User::find($id);

        if (!$user)
            return response()->json([
                'type' => 'warning',
                'text' => __('User not Found'),
            ], 400);

        return response()->json($user);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'username' => 'required|string|max:255',
        ]);

        $user = new User($request->all());
        $user->password = Hash::make('password');
        $user->save();

        $user->address()->create();

        return response()->json([
            'type' => 'success',
            'text' => __('User saved successfully')
        ]);
    }

    public function update(Request $request, $id) {}

    public function delete($id) {}
}
