<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait UserTrait
{
    public function returnResponse($isRefresh)
    {
        $user = Auth::user();
        $token = $isRefresh ? Auth::refresh(false, true) : Auth::tokenById($user->id);
        $expired_in = Auth::factory()->getTTL() * 60;
        $refresh = Auth::setTTL(120)->tokenById($user->id);

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refresh,
            'token_type' => 'Bearer',
            'expires_in' => $expired_in,
            'user' => $this->getUser($user->id, true)
        ]);
    }

    public function getUser(int $id, bool $isView)
    {
        $user = User::find($id);

        if (!$user) return null;

        if ($isView) {
            $user->profile_picture = $user->profile_picture ? Storage::url($user->profile_picture) : 'https://static.vecteezy.com/system/resources/previews/023/547/381/non_2x/user-account-icon-free-vector.jpg';
        }

        return $user;
    }

    public function getCurrentUser(bool $isView)
    {
        return $this->getUser(Auth::user()->id, $isView);
    }

    public function getBasePath(int $user_id)
    {
        $basePath = 'user/' . $user_id . '/';
        if (!Storage::exists($basePath))
            Storage::createDirectory($basePath);

        return $basePath;
    }
}
