<?php

namespace App\Http\Requests\User;

use App\Traits\UserTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    use UserTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAllPermissions(['users', 'users.create']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255', 'in:F,M'],
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique('users')],
            'phone_number.number' => ['nullable', 'string', 'max:255'],
            'phone_number.iso2' => ['nullable', 'string', 'max:255'],
            'address.line_1' => ['nullable', 'string', 'max:255'],
            'address.line_2' => ['nullable', 'string', 'max:255'],
            'address.line_3' => ['nullable', 'string', 'max:255'],
            'address.postcode' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.country' => ['nullable', 'string', 'max:255'],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'string', 'exists:roles,name']
        ];
    }

    public function save()
    {
        $user = $this->addUser([
            'username' => $this->input('username'),
            'name' => $this->input('name'),
            'gender' => $this->input('gender'),
            'email' => $this->input('email'),
            'password' => Hash::make($this->input('username')),
        ], $this->input('address') ?? [], $this->input(('phone_number')) ?? []);

        $user->assignRole($this->input('roles'));
    }
}
