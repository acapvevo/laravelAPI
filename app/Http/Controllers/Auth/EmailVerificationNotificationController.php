<?php

namespace App\Http\Controllers\Auth;

use App\Enums\EmailVerificationStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Auth
 *
 * @subgroup Email Verification Notification
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => EmailVerificationStatus::VERIFIED->value,
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => EmailVerificationStatus::VERIFICATION_LINK_SENT->value]);
    }
}
