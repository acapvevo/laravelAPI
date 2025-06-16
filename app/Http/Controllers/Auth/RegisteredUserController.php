<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @group Auth
 *
 * @subgroup Registered User
 */
class RegisteredUserController extends Controller
{
    use UserTrait;

    /**
     * Handle an incoming registration request.
     *
     * @bodyParam name string required The name of the user.
     * @bodyParam gender string required The gender of the user.
     * @bodyParam email string required The email address of the user.
     * @bodyParam username string required The username of the user.
     * @bodyParam password string required The password of the user.
     * @bodyParam password_confirmation string required The confirmation of the password
     *
     * @unauthenticated
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255', 'in:F,M'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = $this->addUser([
            'name' => $request->name,
            'email' => $request->email_address,
            'username' => $request->username,
            'gender' => $request->gender,
            'password' => Hash::make($request->string('password')),
        ], [], []);

        event(new Registered($user));
        Auth::attempt($request->only(['username', 'password']));

        return $this->returnResponse(true);
    }
}
