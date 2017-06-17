<?php

namespace DoraBoateng\Laravel;

use DoraBoateng\Api\Client;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    /**
     * @var string
     */
    public $urn;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $refreshToken;

    /**
     * @param  array  $data
     * @return \DoraBoateng\Laravel\User|null
     */
    public static function make(array $data = [])
    {
        // Performance check.
        if (! $data || $data['token_type'] !== 'Bearer' || ! $data['access_token']) {
            return null;
        }

        if (! $api = resolve(Client::class)) {
            return null;
        }

        if (! $userData = $api->get('user', [], $data['access_token'])) {
            return null;
        }

        return (new static((array) $userData))->setRememberToken($data['refresh_token']);
    }

    /**
    * @return \DoraBoateng\Laravel\User|null
     */
    public static function retrieveFromSession()
    {
        if (! $properties = session('doraboateng.user')) {
            return null;
        }

        if (! $refreshToken = session('doraboateng.refresh-token')) {
            return null;
        }

        return (new static(json_decode($properties, true)))->setRememberToken($refreshToken);
    }

    /**
     * @param  array  $properties
     */
    protected function __construct(array $properties)
    {
        $this->urn      = $properties['urn'];
        $this->email    = $properties['email'];
        $this->name     = $properties['name'];
    }

    /**
     * Stores the user data in session.
     */
    public function persist()
    {
        // Store user data in session
        session(['doraboateng.user' => json_encode((array) $this)]);
        session(['doraboateng.refresh-token' => $this->getRememberToken()]);

        return $this;
    }

    /**
     * Removes user data from session.
     *
     * @return void
     */
    public function logout()
    {
        session(['doraboateng.user' => null]);
        session(['doraboateng.refresh-token' => null]);

        // TODO: nullify on API?
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->email;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return null;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return \DoraBoateng\Laravel\User
     */
    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;

        return $this;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'refreshToken';
    }
}
