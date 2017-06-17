<?php

namespace DoraBoateng\Laravel\Listeners;

use Illuminate\Contracts\Auth\Authenticatable;

class Logout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(Authenticatable $user)
    {
        if ($user instanceof DoraBoateng\Laravel\User) {
            $user->logout();
        }
    }
}
