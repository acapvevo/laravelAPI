<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\TokenRefreshed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @group Auth
 *
 * @subgroup Authenticated Session
 */
class AuthenticatedSessionController extends Controller
{
    use UserTrait;

    /**
     * Handle an incoming authentication request.
     *
     * @unauthenticated
     *
     * @response {
     *  "access_token": "string",
     * "refresh_token": "string",
     *  "token_type": "Bearer",
     * "token_expires_in": 60,
     * "refresh_expired_in": 120,
     * }
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = User::find(Auth::user()->id);
        $user->last_login = now();

        $user->save();

        return $this->returnResponse(false);
    }

    /**
     * Refresh Session.
     *
     * @bodyParam refresh_token string required refresh token
     * @bodyParam access_token string required access token
     *
     * @response {
     *  "access_token": "string",
     * "refresh_token": "string",
     *  "token_type": "Bearer",
     * "token_expires_in": 60,
     * "refresh_expired_in": 120,
     * }
     *
     * @unauthenticated
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
            'access_token' => 'required|string',
        ]);

        Auth::setToken($request->input('refresh_token'));
        TokenRefreshed::dispatch(Auth::user());

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
