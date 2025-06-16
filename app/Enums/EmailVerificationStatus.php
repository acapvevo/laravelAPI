<?php

namespace App\Enums;

enum EmailVerificationStatus: string
{
    case VERIFIED = 'verified';
    case VERIFICATION_REQUIRED = 'verification_required';
    case VERIFICATION_LINK_SENT = 'verification_link_sent';
}
