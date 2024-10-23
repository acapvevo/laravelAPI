<?php

namespace App\Enums;

use Rexlabs\Enum\Enum;

/**
 * The Gender enum.
 *
 * @method static self MALE()
 * @method static self FEMALE()
 */
class Gender extends Enum
{
    const MALE = 'M';
    const FEMALE = 'F';
}
