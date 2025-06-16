<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest as EmailVerificationRequestBase;

class EmailVerificationRequest extends EmailVerificationRequestBase
{
    public function bodyParameters(): array
    {
        return [
            'id' => [
                'description' => 'The ID of the user.',
                'example' => 1,
            ],
            'hash' => [
                'description' => 'The hash of the email verification link.',
                'example' => 'abc123xyz',
            ],
        ];
    }
}
