<?php

namespace App\Traits;

use App\Models\Address;
use App\Models\PhoneNumber;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait UserTrait
{
    public function returnResponse($isRefresh)
    {
        $user = User::find(Auth::user()->id);
        $token = $isRefresh ? Auth::refresh(false, true) : Auth::tokenById($user->id);
        $token_expired_in = Auth::factory()->getTTL();
        $refresh = Auth::setTTL(120)->tokenById($user->id);
        $refresh_expired_in = Auth::factory()->getTTL();

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refresh,
            'token_type' => 'Bearer',
            'token_expires_in' => $token_expired_in,
            'refresh_expired_in' => $refresh_expired_in,
            'user' => $this->getFullUserInfo($user->id, true),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'roles' => $user->getRoleNames(),
        ]);
    }

    public function getFullUserInfo(int $id, bool $isView)
    {
        $user = User::find($id);

        if (! $user) {
            return null;
        }

        if ($isView) {
            $user->profile_picture = $user->getProfilePictureUrl();
        }

        return $user;
    }

    public function getCurrentUser(bool $isView)
    {
        return $this->getFullUserInfo(Auth::user()->id, $isView);
    }

    public function getFilePath(int $user_id)
    {
        $basePath = 'user/'.$user_id.'/';
        if (! Storage::exists($basePath)) {
            Storage::createDirectory($basePath);
        }

        return $basePath;
    }

    public function addUser(array $_user, array $_address, array $_phone_number)
    {
        $user = new User($_user);
        $user->save();

        $phone_number = new PhoneNumber($_phone_number);
        $user->phone_number()->save($phone_number);

        $address = new Address($_address);
        $user->address()->save($address);

        return $user;
    }
}
