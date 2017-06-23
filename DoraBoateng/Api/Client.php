<?php

namespace DoraBoateng\Api;

use Sentry;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use DoraBoateng\Api\Exceptions\Exception        as ApiException;
use DoraBoateng\Api\Exceptions\Configuration    as ConfigException;
use DoraBoateng\Api\Exceptions\InvalidRequest;

class Client
{
    const API_VERSION               = '0.4';

    const ERROR_INVALID_ID          = 10;
    const ERROR_INVALID_CODE        = 11;
    const ERROR_INVALID_QUERY       = 12;
    const ERROR_INVALID_RESOURCE    = 13;

    const EVENT_SET_ACCESS_TOKEN    = 'accesstoken.store';
    const EVENT_GET_ACCESS_TOKEN    = 'accesstoken.retrieve';

    const RESOURCE_TYPES = [
        'culture',
        'definition',
        'language',
        'languagefamily',
        'reference',
        'tag',
    ];

    /**
     *
     */
    public $client;

    /**
     *
     */
    protected $clientId;

    /**
     *
     */
    protected $secret;

    /**
     * @var array
     */
    protected $events = [];

    /**
     *
     */
    protected $errors = [];

    /**
     * @param  array $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        // Set client credentials
        if (empty($config['id']) || empty($config['secret'])) {
            throw new ConfigException('Invalid API Credentials.');
        }

        $this->clientId = $config['id'];
        $this->secret   = $config['secret'];

        // Configure base URI
        $endpoint = isset($config['api_host']) ? $config['api_host'] : 'https://api.doraboateng.com';
        $endpoint .= '/'.static::API_VERSION.'/';

        $config['base_uri'] = $endpoint;

        $this->client = new GuzzleClient(array_except($config, ['api_host', 'id', 'secret']));
    }

    /**
     * Retrieves a definition.
     *
     * @param  int   $id     Definition ID
     * @param  array $embed  Relations to include with definition
     * @throws InvalidRequest
     * @return stdClass
     */
    public function getDefinition($id, array $embed = [])
    {
        if (! $this->isValidId($id)) {
            throw new InvalidRequest('"'.$id.'" is not a valid ID');
        }

        return $this->get('definitions/'.$id, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     * Searches the API for definitions.
     *
     * @param  string  $query
     * @param  string  $langCode
     * @throws InvalidRequest
     * @return stdClass
     */
    public function searchDefinitions($query, $langCode = null)
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            throw new InvalidRequest('Query string too short', static::ERROR_INVALID_QUERY);
        }

        if ($langCode && ! $this->isValidLanguageCode($langCode)) {
            throw new InvalidRequest('Invalid language code', static::ERROR_INVALID_CODE);
        }

        return $this->get('definitions/search/'.urlencode($query), [
            'lang' => $langCode,
        ]);
    }

    /**
     * @param  int|string  $langId
     * @param  array       $embed
     * @throws InvalidRequest
     * @return stdClass
     */
    public function getRandomDefinition($langId = null, array $embed = [])
    {
        if ($langId) {
            if (is_numeric($langId) && ! $this->isValidId($langId)) {
                throw new InvalidRequest('Invalid language identifier', static::ERROR_INVALID_ID);
            } elseif (! $this->isValidLanguageCode($langId)) {
                throw new InvalidRequest('Invalid language code', static::ERROR_INVALID_CODE);
            }
        } else {
            $langId = '';
        }

        return $this->get('definitions/random/'.$langId, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     * @param  int|string  $id
     * @param  array       $embed
     * @throws InvalidRequest
     * @return stdClass
     */
    public function getLanguage($id, array $embed = [])
    {
        if (! $this->isValidId($id) && ! $this->isValidLanguageCode($id)) {
            throw new InvalidRequest('Invalid language code', static::ERROR_INVALID_CODE);
        }

        return $this->get('languages/'.$id, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     * @return stdClass
     */
    public function getLanguageOfTheWeek(array $embed = [])
    {
        return $this->get('languages/weekly', [
            'embed' => $embed ? implode(',', $embed) : null
        ]);
    }

    /**
     * @param  string $query
     * @throws InvalidRequest
     * @return array
     */
    public function search($query)
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            throw new InvalidRequest('Query string too short', static::ERROR_INVALID_QUERY);
        }

        return $this->get('search/'.$query);
    }

    /**
     * @param  string  $endpoint
     * @param  array   $query
     * @param  string  $token
     */
    public function get($endpoint, array $query = [], $token = null)
    {
        // TODO: we shouldn't have to specify the token to use.
        $token = $token ?: $this->getAccessToken();

        try {
            $response = $this->client->get($endpoint, [
                'query'     => $query,
                'headers'   => [
                    'Accept'        => 'application/json',
                    'Authorization' => "Bearer {$token}",
                ],
            ]);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 401:
                case 500:
                case 503:
                    Sentry::captureException($e);
                    return null;
                    break;

                // Unhandled exception
                default:
                    throw $e;
            }
        } catch (Exception $e) {
            // TODO: handle
            throw $e;
        }

        if ($response->getStatusCode() !== 200) {
            // TODO: handle
            throw new \Exception('HTTP Error: '. $response->getStatusCode());
        }

        if (! $response->hasHeader('Content-Type') ||
            ! in_array('application/json', $response->getHeader('Content-Type'))
        ) {
            // TODO: handle
            throw new \Exception('Invalid Content-Type');
        }

        $data = json_decode((string) $response->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            // TODO: handle
            throw new \Exception('JSON Error: '. json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param  string  $name
     * @param  mixed   $callback
     * @return static
     */
    public function addListener($name, $callback)
    {
        if (! $name) {
            throw new ConfigException('Invalid listener.');
        }

        if (! array_key_exists($name, $this->events)) {
            $this->events[$name] = [];
        }

        array_push($this->events[$name], $callback);

        return $this;
    }

    /**
     * Retrieves an access token based on the password grant.
     *
     * @param  string  $username
     * @param  string  $password
     * @param  string  $scope
     * @return array|false
     */
    public function getPasswordAccessToken($username, $password, $scope = 'user-read')
    {
        try {
            $response = $this->client->post('/oauth/token', [
                'form_params' => [
                    'grant_type'    => 'password',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->secret,
                    'username'      => $username,
                    'password'      => $password,
                    'scope'         => $scope,
                ]
            ]);
        } catch (Exception $e) {
            return $this->addError($e->getMessage());
        }

        $data = json_decode((string) $response->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->addError(json_last_error_msg());
        }

        return $data;
    }

    protected function getAccessToken()
    {
        if (! $token = $this->fireEvent(static::EVENT_GET_ACCESS_TOKEN)) {
            try {
                $response = $this->client->post('/oauth/token', [
                    'form_params' => [
                        'grant_type'    => 'client_credentials',
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->secret,
                        'scope'         => 'resource-read resource-write',
                    ]
                ]);
            } catch (Exception $e) {
                // TODO: handle
                throw $e;
            }

            $data = json_decode((string) $response->getBody());

            if (json_last_error() !== JSON_ERROR_NONE) {
                // TODO: handle
                throw new ApiException('JSON Error: '. json_last_error_msg());
            }

            $this->fireEvent(static::EVENT_SET_ACCESS_TOKEN, [
                'token-type'    => $data->token_type,
                'expires'       => $data->expires_in,
                'access-token'  => $token = $data->access_token,
            ]);
        }

        return $token;
    }

    /**
     * @param  string $msg
     * @return false
     */
    protected function addError($msg)
    {
        $this->errors[] = $msg;

        return false;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return end($this->errors);
    }

    /**
     * @param  int|string $id
     * @return bool
     */
    protected function isValidId($id)
    {
        $sanitizedId = (int) $id;

        if ($sanitizedId < 1) {
            return false;
        }

        return is_string($id)
                ? (string) $sanitizedId === $id
                : $sanitizedId === $id;
    }

    /**
     * @param  string $code
     * @return bool
     */
    protected function isValidLanguageCode($code)
    {
        if (! is_string($code)) {
            return false;
        }

        // A language code can contain letters and dashes.
        $sanitizedCode = preg_replace('/[^a-z\-]/', '', strtolower($code));

        if (strtolower($code) !== $sanitizedCode) {
            return false;
        }

        // And will have the format "abc" or "abc-def"
        return 1 === preg_match('/^([a-z]{3}(-[a-z]{3})?)$/', $code);
    }

    /**
     * @param  string $resourceName
     * @return bool
     */
    protected function validateResourceName($resourceName)
    {
        if (! is_string($resourceName)) {
            return false;
        }

        $resourceName = str_replace('_', '', strtolower($resourceName));
        $sanitized    = preg_replace('/[^a-z]/', '', $resourceName);

        if (strlen($sanitized) < 1 || $sanitized != $resourceName) {
            return false;
        }

        return in_array($sanitized, self::RESOURCE_TYPES) ? $sanitized : false;
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return mixed
     */
    protected function fireEvent($name, array $arguments = [])
    {
        $result = null;

        if (! isset($this->events[$name])) {
            return $result;
        }

        foreach ($this->events[$name] as $callable) {
            if (! is_callable($callable)) {
                continue;
            }

            $result = call_user_func_array($callable, $arguments);
        }

        return $result;
    }
}
