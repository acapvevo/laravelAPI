<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if(! $request->expectsJson() && $request->is('super_admin*')) {
            return redirect()->intended(
                config('app.frontend_url').'/super_admin/login'
            );
        }
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
