<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

/**
 * @group Profile
 */
class ProfileController extends Controller
{
    use UserTrait;

    /**
     * Display the authenticated user's profile information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json($this->getFullUserInfo(Auth::user()->id, true));
    }

    /**
     * Upload and update the user's profile picture.
     *
     *
     * @bodyParam profile_picture file required The profile picture file to upload. Must be an image (jpeg, png, jpg) with a maximum size of 2MB.
     *
     * @return \Illuminate\Http\Response
     */
    public function picture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $this->getCurrentUser(false);

        $manager = new ImageManager(new Driver);
        $image = $manager->read($request->file('profile_picture'));
        $image->resize(300, 200)->toJpeg();

        $basePath = $this->getFilePath($user->id);
        $profilePicturePath = $basePath.'profile_picture.jpg';

        Storage::put($profilePicturePath, $image->encode());

        $user->profile_picture = $profilePicturePath;
        $user->save();

        return response()->noContent();
    }

    /**
     * Update the authenticated user's profile information.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $request->save();

        return response()->noContent();
    }

    /**
     * Update the authenticated user's password.
     *
     *
     * @bodyParam password string required The new password. Must be at least 8 characters long, contain mixed case letters and numbers.
     * @bodyParam password_confirmation string required The confirmation of the new password.
     *
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = $this->getCurrentUser(false);

        $user->password = Hash::make($request->input('password'));

        $user->save();

        return response()->noContent();
    }
}
