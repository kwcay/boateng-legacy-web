<?php

namespace DoraBoateng\Laravel;

use Illuminate\Contracts\Session\Session;
use DoraBoateng\Laravel\User as UserFactory;
use Illuminate\Auth\GuardHelpers as GuardTrait;
use Illuminate\Contracts\Auth\Guard as Contract;

class Guard implements Contract
{
    use GuardTrait;

    /**
     * @var \Illuminate\Contracts\Session\Session
     */
    protected $session;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->inputKey = 'api_token';
        $this->storageKey = 'api_token';
    }

    // public function __construct(Session $session)
    // {
    //     $this->session = $session;
    // }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        //
        if ($this->user) {
            return $this->user;
        }

        // Retrieve user data from session
        if ($stored = $this->session->get('doraboateng.user')) {
            $this->user = UserFactory::make($stored);

            return $this->user;
        }

        // Retrieve user from API
        if ($token = $this->session->get('doraboateng.access-token')) {

        }

        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return false;
    }
}
