<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    use UserTrait;

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = User::find(Auth::user()->id);
        $user->last_login = now();
        $user->timezone = $request->input('timezone');

        $user->save();

        return $this->returnResponse(false);
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        Auth::setToken($request->input('refresh_token'));

        return $this->returnResponse(true);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(): Response
    {
        Auth::logout();

        return response()->noContent();
    }
}
