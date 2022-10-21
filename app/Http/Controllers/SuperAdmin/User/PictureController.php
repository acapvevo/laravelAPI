<?php

namespace App\Http\Controllers\SuperAdmin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PictureController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::guard('super_admin')->user();

        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $imageName = $user->id . '.' . $request->image->extension();
        $request->image->storeAs('profile_picture/super_admin', $imageName);

        $user->image = $imageName;

        $user->save();

        return response()->noContent();
    }

    public function show()
    {
        $user = Auth::guard('super_admin')->user();

        return Storage::response('profile_picture/super_admin/' . $user->image);
    }
}
