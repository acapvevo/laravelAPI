<?php

namespace App\Http\Controllers\SuperAdmin\User;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function view()
    {
        $user = Auth::guard('super_admin')->user();

        $user->setImageURL();

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('super_admin')->user();

        $request->validate([
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                'string',
                Rule::unique('super_admins')->ignore($user),
            ]
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Your User Profile updated Successfully'
        ]);
    }
}
