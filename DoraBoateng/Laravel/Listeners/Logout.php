<?php

namespace DoraBoateng\Laravel\Listeners;

use Illuminate\Contracts\Auth\Authenticatable;

class Logout
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
     *
     * @param  Authenticatable $user
     */
    public function handle(Authenticatable $user)
    {
        if ($user instanceof \DoraBoateng\Laravel\User) {
            $user->logout();
        }
    }
}
