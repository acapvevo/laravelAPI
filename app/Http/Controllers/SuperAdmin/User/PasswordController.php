<?php

namespace App\Http\Controllers\SuperAdmin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::guard('super_admin')->user();

        $request->validate([
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user->password = Hash::make($request->password);

        $user->save();

        return response()->noContent();
    }
}
