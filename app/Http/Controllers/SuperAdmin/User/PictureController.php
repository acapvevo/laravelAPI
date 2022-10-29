<?php

namespace App\Http\Controllers\SuperAdmin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PictureController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $user = Auth::guard('super_admin')->user();

        $imageName = $user->id . '.' . $request->image->extension();
        $imagePath = "app\\profile_picture\\super_admin";

        $img = Image::make($request->image);
        if(!Storage::exists("profile_picture\\super_admin")) {
            Storage::makeDirectory("profile_picture\\super_admin"); //creates directory
        }
        $img->fit(200)->save(storage_path($imagePath . '\\' . $imageName));

        $user->image = $imageName;

        $user->save();

        return response()->noContent();
    }
}
