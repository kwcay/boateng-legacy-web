<?php

namespace DoraBoateng\Laravel;

use DoraBoateng\Api\Client;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    /**
     * @const string
     */
    const SESSION_USER = '';

    /**
     * @const string
     */
    const SESSION_ACCESS_TOKEN  = 'doraboateng.access-token';

    /**
     * @const string
     */
    const SESSION_REFRESH_TOKEN = 'doraboateng.refresh-token';

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
    public $accessToken;

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

        return (new static((array) $userData))
            ->setRememberToken($data['refresh_token'])
            ->setAccessToken($data['access_token']);
    }

    /**
    * @return \DoraBoateng\Laravel\User|null
     */
    public static function retrieveFromSession()
    {
        if (! $properties = session(static::SESSION_USER)) {
            return null;
        }

        if (! $accessToken = session(static::SESSION_ACCESS_TOKEN)) {
            return null;
        }

        if (! $refreshToken = session(static::SESSION_REFRESH_TOKEN)) {
            return null;
        }

        return (new static(json_decode($properties, true)))
            ->setRememberToken($refreshToken)
            ->setAccessToken($accessToken);
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
     *
     * @return \DoraBoateng\Laravel\User
     */
    public function persist()
    {
        // Store user data in session
        session([static::SESSION_USER           => json_encode((array) $this)]);
        session([static::SESSION_REFRESH_TOKEN  => $this->getRememberToken()]);
        session([static::SESSION_ACCESS_TOKEN   => $this->getAccessToken()]);

        return $this;
    }

    /**
     * Removes user data from session.
     *
     * @return void
     */
    public function logout()
    {
        session([static::SESSION_USER           => null]);
        session([static::SESSION_REFRESH_TOKEN  => null]);
        session([static::SESSION_ACCESS_TOKEN   => null]);

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
     * @return string
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Retrieves the access token associated with this user.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Stores the access token associated with this user.
     *
     * @param  string  $value
     * @return \DoraBoateng\Laravel\User
     */
    public function setAccessToken($value)
    {
        $this->accessToken = $value;

        return $this;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getAccessToken();
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

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
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
}
