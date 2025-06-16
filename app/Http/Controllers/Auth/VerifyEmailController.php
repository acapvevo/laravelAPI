<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\Verified;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Enums\EmailVerificationStatus;
use App\Http\Requests\Auth\EmailVerificationRequest;

/**
 * @group Auth
 *
 * @subgroup Email Verification
 */
class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        if (! $request->user()->hasVerifiedEmail() && $request->user()->markEmailAsVerified()) {
            Verified::dispatch($request->user());
        }

        return response()->json([
            'status' => EmailVerificationStatus::VERIFIED->value,
        ]);
    }
}
