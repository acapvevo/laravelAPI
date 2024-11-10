<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255', 'in:F,M'],
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number.number' => ['nullable', 'string', 'max:255'],
            'phone_number.iso2' => ['nullable', 'string', 'max:255'],
            'address.line_1' => ['nullable', 'string', 'max:255'],
            'address.line_2' => ['nullable', 'string', 'max:255'],
            'address.line_3' => ['nullable', 'string', 'max:255'],
            'address.postcode' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.country' => ['nullable', 'string', 'max:255']
        ];
    }

    public function save()
    {
        $user = User::find($this->user()->id);

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
    }
}
