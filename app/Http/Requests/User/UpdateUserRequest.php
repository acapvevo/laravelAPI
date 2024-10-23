<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Enums\Gender;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email_address' => ['required', 'string', 'max:255', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'address.line_1' => ['required', 'string', 'max:255'],
            'address.line_2' => ['required', 'string', 'max:255'],
            'address.line_3' => ['nullable', 'string', 'max:255'],
            'address.postcode' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.state' => ['required', 'string', 'max:255'],
            'address.country' => ['required', 'array'],
            'address.country.iso2' => ['required', 'string', 'max:255'],
            'address.country.name' => ['required', 'string', 'max:255']
        ];
    }

    public function save()
    {
        $user = User::find($this->user()->id);

        $user->name = $this->input('name');
        $user->email_address = $this->input('email_address');
        $user->phone_number = $this->input('phone_number');
        $user->gender = $this->input('gender');

        $user->save();

        $user->address->line_1 = $this->input('address.line_1');
        $user->address->line_2 = $this->input('address.line_2');
        $user->address->line_3 = $this->input('address.line_3') ?? '';
        $user->address->postcode = $this->input('address.postcode');
        $user->address->city = $this->input('address.city');
        $user->address->state = $this->input('address.state');

        $user->address->save();

        $user->address->country->iso2 = $this->input('address.country.iso2');
        $user->address->country->name = $this->input('address.country.name');
        
        $user->address->country->save();
    }
}
