<?php

namespace App\Listeners\Auth;

use App\Events\Auth\Verified;

class SendVerifiedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        //
    }
}
