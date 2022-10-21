<?php

namespace App\Http\Controllers\SuperAdmin\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SuperAdmin\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\SuperAdmin\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user('super_admin')->update([
            'login_at' => config('time.current')
        ]);
        $request->user('super_admin')->setImageURL();

        $token =  $request->user('super_admin')->createToken('API Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $request->user('super_admin')
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('super_admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
