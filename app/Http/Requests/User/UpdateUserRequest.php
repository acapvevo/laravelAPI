<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAllPermissions(['users', 'users.edit']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user_id = $this->route('id');

        return [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user_id)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255', 'in:F,M'],
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique('users')->ignore($user_id)],
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
            'roles.*' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function bodyParameters()
    {
        return [
            'username' => [
                'description' => __('The username of the user.'),
                'example' => 'johndoe',
            ],
            'name' => [
                'description' => __('The full name of the user.'),
                'example' => 'John Doe',
            ],
            'gender' => [
                'description' => __('The gender of the user.'),
                'example' => 'M',
            ],
            'email' => [
                'description' => __('The email address of the user.'),
                'example' => 'johndoe@example.com',
            ],
            'phone_number' => [
                'description' => __('The phone number information of the user.'),
                'example' => [
                    'number' => '+1234567890',
                    'iso2' => 'US',
                ],
            ],
            'phone_number.number' => [
                'description' => __('The phone number of the user.'),
                'example' => '+1234567890',
            ],
            'phone_number.iso2' => [
                'description' => __('The ISO 3166-1 alpha-2 country code of the user\'s phone number.'),
                'example' => 'US',
            ],
            'address' => [
                'description' => __('The address of the user.'),
                'example' => [
                    'line_1' => '123 Main St',
                    'line_2' => 'Apt 4B',
                    'line_3' => '',
                    'postcode' => '12345',
                    'city' => 'Anytown',
                    'state' => 'CA',
                    'country' => 'USA',
                ],
            ],
            'address.line_1' => [
                'description' => __('The first line of the user\'s address.'),
                'example' => '123 Main St',
            ],
            'address.line_2' => [
                'description' => __('The second line of the user\'s address (optional).'),
                'example' => 'Apt 4B',
            ],
            'address.line_3' => [
                'description' => __('The third line of the user\'s address (optional).'),
                'example' => '',
            ],
            'address.postcode' => [
                'description' => __('The postcode of the user\'s address.'),
                'example' => '12345',
            ],
            'address.city' => [
                'description' => __('The city of the user\'s address.'),
                'example' => 'Anytown',
            ],
            'address.state' => [
                'description' => __('The state of the user\'s address.'),
                'example' => 'Carifornia',
            ],
            'address.country' => [
                'description' => __('The country of the user\'s address.'),
                'example' => 'USA',
            ],
            'roles' => [
                'description' => __('The roles assigned to the user.'),
                'example' => ['Admin', 'User'],
            ],
            'roles.*' => [
                'description' => __('The name of a role assigned to the user.'),
                'example' => 'Admin',
            ],
        ];
    }

    public function save()
    {
        $user = User::find($this->route('id'));

        $user->username = $this->input('username');
        $user->name = $this->input('name');
        $user->email = $this->input('email');
        $user->gender = $this->input('gender');

        $user->save();

        $user->phone_number->iso2 = $this->input('phone_number.iso2');
        $user->phone_number->number = $this->input('phone_number.number');

        $user->phone_number->save();

        $user->address->line_1 = $this->input('address.line_1');
        $user->address->line_2 = $this->input('address.line_2');
        $user->address->line_3 = $this->input('address.line_3');
        $user->address->postcode = $this->input('address.postcode');
        $user->address->city = $this->input('address.city');
        $user->address->state = $this->input('address.state');
        $user->address->country = $this->input('address.country');

        $user->address->save();

        $user->syncRoles($this->input('roles'));
    }
}
