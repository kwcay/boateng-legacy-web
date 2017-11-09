<?php

namespace App\Http\Middleware;

use Closure;

class TracksRequests extends \Frnkly\LaravelKeen\Middleware
{
    /**
     * Determines if the middleware should run or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response $response
     * @return bool
     */
    protected function shouldRun($request, $response) : bool
    {
        if (! parent::shouldRun($request, $response)) {
            return false;
        }

        if (! $user = $request->user()) {
            return true;
        }

        // Skip tracking for some users
        if (strpos($user->email, '@doraboateng.com') !== false) {
            return false;
        }

        return true;
    }
}
