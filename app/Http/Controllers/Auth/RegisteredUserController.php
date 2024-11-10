<?php

namespace App\Http\Controllers\Auth;

use App\Models\Country;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    use UserTrait;

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255', 'in:F,M'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
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
