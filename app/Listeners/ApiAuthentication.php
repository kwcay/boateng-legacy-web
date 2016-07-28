<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
 *
 * @brief   Determines the authentication method for API requests.
 */
namespace App\Listeners;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Events\RouteMatched;

class ApiAuthentication
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Route $route, Request $request)
    {
        $this->route = $route;
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  RouteMatched  $event
     * @return void
     */
    public function handle(RouteMatched $event)
    {
        if ($this->route->getPrefix() == 'api/0.1') {
            // Authenticate through OAuth2.
            if ($this->request->headers->has('Authorization') || $this->request->has('access_token')) {
                $this->route->middleware(\LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class);
            }

            // Authenticate through Laravel.
            else {
                $this->route->middleware(\App\Http\Middleware\Authenticate::class);
            }
        }
    }
}
