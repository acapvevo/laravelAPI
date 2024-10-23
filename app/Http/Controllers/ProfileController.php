<?php

namespace App\Http\Controllers;

use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\User\UpdateUserRequest;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    use UserTrait;

    public function index()
    {
        return response()->json($this->getUser(Auth::user()->id, true));
    }

    public function picture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = $this->getCurrentUser(false);

        $image = Image::read($request->file('profile_picture'));
        $image->resize(300, 200)->toJpeg();

        $basePath = $this->getBasePath($user->id);
        Storage::put($basePath . 'profile_picture.jpg', $image->encode());

        $user->profile_picture = $basePath . 'profile_picture.jpg';
        $user->save();

        return response()->json([
            "text" => __("Profile picture successfully updated"),
            "type" => "success",
            "user" => $this->getCurrentUser(true)
        ]);
    }

    public function update(UpdateUserRequest $request)
    {
        $request->save();

        return response()->json([
            "text" => __("Profile successfully updated"),
            "type" => "success",
            "user" => $this->getCurrentUser(true)
        ]);
    }

    public function password(Request $request) {
        $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()]
        ]);

        $user = $this->getCurrentUser(false);

        $user->password = Hash::make($request->input('password'));

        $user->save();

        return response()->json([
            "text" => __("Password successfully updated"),
            "type" => "success"
        ]);
    }
}
