<?php

namespace App\Listeners\Auth;

use App\Events\Auth\TokenRefreshed;

class SendTokenRefreshedNotification
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
    public function handle(TokenRefreshed $event): void
    {
        //
    }
}
