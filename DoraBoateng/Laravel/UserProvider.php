<?php

namespace DoraBoateng\Laravel;

use DoraBoateng\Api\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as Contract;

class UserProvider implements Contract
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        if (! $user = User::retrieveFromSession()) {
            return null;
        }

        if ($user->email !== $identifier) {
            return null;
        }

        return $user;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        dd(
            __CLASS__.'::'.__FUNCTION__,
            func_get_args()
        );
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        try {
            $token = $this->api()->getPasswordAccessToken(
                $credentials['email'],
                $credentials['password'],
                'resource-read resource-write user-read user-write'
            );
        } catch (\Exception $e) {
            return null;
        }

        return $token ? User::make((array) $token)->persist() : null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Simply validate the the user returned by the API is the same as the one given.
        return $user->email === $credentials['email'];
    }

    /**
     * @return \Doraboateng\Api\Client
     */
    private function api()
    {
        return resolve(Client::class);
    }
}
