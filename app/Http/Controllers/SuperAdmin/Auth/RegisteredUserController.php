<?php

namespace App\Http\Controllers\SuperAdmin\Auth;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:super_admins',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $super_admin = SuperAdmin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'super_admin.verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        event(new Registered($super_admin));

        Auth::guard('super_admin')->login($super_admin);

        $token = Auth::guard('super_admin')->user()->createToken('API Token');

        return response()->json([
            'token' => $token->accessToken,
            'user' => $request->user('super_admin'),
            'expiresIn' =>  $token->token->expires_at->diffInMinutes(Carbon::now())
        ]);
    }
}
