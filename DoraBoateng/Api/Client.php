<?php

namespace DoraBoateng\Api;

use Sentry;
use Exception;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use DoraBoateng\Api\Exceptions\Exception        as ApiException;
use DoraBoateng\Api\Exceptions\Configuration    as ConfigException;
use DoraBoateng\Api\Exceptions\InvalidRequest   as InvalidRequestException;

class Client
{
    const API_VERSION               = '0.5';
    const API_HOST                  = 'https://api.doraboateng.com';

    const ERROR_INVALID_ID          = 10;
    const ERROR_INVALID_CODE        = 11;
    const ERROR_INVALID_QUERY       = 12;
    const ERROR_INVALID_RESOURCE    = 13;

    const EVENT_SET_ACCESS_TOKEN    = 'accesstoken.store';
    const EVENT_GET_ACCESS_TOKEN    = 'accesstoken.retrieve';
    const EVENT_RESPONSE            = 'response';
    const EVENT_CLIENT_EXCEPTION    = 'client-exception';
    const EVENT_EXCEPTION           = 'exception';

    const RESOURCE_TYPES = [
        'culture',
        'definition',
        'language',
        'languagefamily',
        'reference',
        'tag',
    ];

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $events   = [];

    /**
     * @var array
     */
    protected $errors   = [];

    /**
     * @var bool
     */
    protected $debug    = false;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param  array $config Configures the Guzzle client.
     * @throws \DoraBoateng\Api\Exceptions\Configuration
     */
    public function __construct(array $config = [])
    {
        // Set client credentials
        if (empty($config['id']) || empty($config['secret'])) {
            throw new ConfigException('Invalid API Credentials.');
        }

        $this->clientId = $config['id'];
        $this->secret   = $config['secret'];
        $this->debug    = isset($config['debug']) && $config['debug'];

        // Configure base URI
        $config['base_uri'] = (@$config['api_host'] ?: static::API_HOST).'/'.static::API_VERSION.'/';

        $this->client = new GuzzleClient(array_diff_key($config, array_flip([
            'api_host',
            'id',
            'secret',
            'debug'
        ])));
    }

    /**
     * @param  string $token
     * @param  string $method
     * @param  string $endpoint
     * @param  array  $options
     * @param  int    $tries
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \Exception
     */
    public function request(
        $token,
        $endpoint,
        $method,
        array $options = [],
        $tries = 2
    ) {
        $token = $token ?: $this->getAccessToken();

        // Set required headers.
        $options['headers']['Accept'] = 'application/json,text/html';
        $options['headers']['Authorization'] = "Bearer {$token}";
        $options['on_stats'] = array($this, 'handleTransferStats');

        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (ClientException $e) {
            // Retrieve a new access token and try again.
            if ($e->getResponse()->getStatusCode() === 401
                && $tries > 0
                && $method === 'GET'
            ) {
                return $this->request($this->getAccessToken(true), $endpoint, $method, $options, --$tries);
            }

            return $this->handleClientException($e);
        } catch (Exception $e) {
            return $this->handleGeneralException($e);
        }

        if (! $response->hasHeader('Content-Type') ||
            ! in_array('application/json', $response->getHeader('Content-Type'))
        ) {
            throw new ApiException('Invalid Content-Type');
        }

        $data = json_decode((string) $response->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->addError(json_last_error_msg());
        }

        return $data;
    }

    /**
     * Performs a GET request on the API.
     *
     * @param  string  $endpoint
     * @param  array   $query
     * @param  string  $token
     * @return \stdClass
     */
    public function get($endpoint, array $query = [], $token = null)
    {
        return $this->request($token, $endpoint, 'GET', [
            'query' => $query,
        ]);
    }

    /**
     * Performs a POST request on the API.
     *
     * @param  string  $token
     * @param  string  $endpoint
     * @param  array   $data
     * @return \stdClass
     */
    public function post($token, $endpoint, array $data)
    {
        return $this->request($token, $endpoint, 'POST', [
            'form_params' => $data
        ]);
    }

    /**
     * Performs a PUT request on the API.
     *
     * @param  string  $token
     * @param  string  $endpoint
     * @param  array   $data
     * @return \stdClass
     */
    public function put($token, $endpoint, array $data = [])
    {
        return $this->request($token, $endpoint, 'PUT', [
            'form_params' => $data
        ]);
    }

    /**
     * Performs a PATCH request on the API.
     *
     * @param  string  $token
     * @param  string  $endpoint
     * @param  array   $data
     * @return \stdClass
     */
    public function patch($token, $endpoint, array $data = [])
    {
        return $this->request($token, $endpoint, 'PATCH', [
            'form_params' => $data
        ]);
    }

    /**
     * Performs a DELETE request on the API.
     *
     * @param  string  $token
     * @param  string  $endpoint
     * @return \stdClass
     */
    public function destroy($token, $endpoint)
    {
        return $this->request($token, $endpoint, 'DELETE');
    }

    /**
     * Retrieves a definition.
     *
     * @param  int   $id     Definition ID
     * @param  array $embed  Relations to include with definition
     * @return \stdClass
     *
     * @throws \DoraBoateng\Api\Exceptions\InvalidRequest
     */
    public function getDefinition($id, array $embed = [])
    {
        if (! $this->isValidId($id)) {
            throw new InvalidRequestException('"'.$id.'" is not a valid identifier.');
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
     * @return \stdClass
     *
     * @throws \DoraBoateng\Api\Exceptions\InvalidRequest
     */
    public function searchDefinitions($query, $langCode = null)
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            throw new InvalidRequestException('Query string too short', static::ERROR_INVALID_QUERY);
        }

        if ($langCode && ! $this->isValidLanguageCode($langCode)) {
            throw new InvalidRequestException('Invalid language code', static::ERROR_INVALID_CODE);
        }

        return $this->get('definitions/search', [
            'lang'  => $langCode,
            'q'     => urlencode($query),
        ]);
    }

    /**
     * @param  int|string  $langId
     * @param  array       $embed
     * @return \stdClass
     *
     * @throws \DoraBoateng\Api\Exceptions\InvalidRequest
     */
    public function getRandomDefinition($langId = null, array $embed = [])
    {
        if ($langId) {
            if (is_numeric($langId) && ! $this->isValidId($langId)) {
                throw new InvalidRequestException('Invalid language identifier', static::ERROR_INVALID_ID);
            } elseif (! $this->isValidLanguageCode($langId)) {
                throw new InvalidRequestException('Invalid language code', static::ERROR_INVALID_CODE);
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
     * @return \stdClass
     *
     * @throws \DoraBoateng\Api\Exceptions\InvalidRequest
     */
    public function getLanguage($id, array $embed = [])
    {
        if (! $this->isValidId($id) && ! $this->isValidLanguageCode($id)) {
            throw new InvalidRequestException('Invalid language code', static::ERROR_INVALID_CODE);
        }

        return $this->get('languages/'.$id, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     * @param  array $embed
     * @return \stdClass
     */
    public function getLanguageOfTheWeek(array $embed = [])
    {
        return $this->get('languages/weekly', [
            'embed' => $embed ? implode(',', $embed) : null
        ]);
    }

    /**
     * @param  string $query
     * @return array
     *
     * @throws \DoraBoateng\Api\Exceptions\InvalidRequest
     */
    public function search($query)
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            throw new InvalidRequestException('Query string too short', static::ERROR_INVALID_QUERY);
        }

        return $this->get('search/'.urlencode($query));
    }

    /**
     * @param  string  $name
     * @param  mixed   $callback
     * @return static
     *
     * @throws \DoraBoateng\Api\Exceptions\Configuration
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
        return $this->getRawTokenData([
            'grant_type'    => 'password',
            'client_id'     => $this->clientId,
            'client_secret' => $this->secret,
            'username'      => $username,
            'password'      => $password,
            'scope'         => $scope,
        ]);
    }

    /**
     * Retrieves an access token based on the client credentials grant.
     *
     * @param  bool $forceNew
     * @return string|null
     */
    protected function getAccessToken($forceNew = false)
    {
        if (! $forceNew && $token = $this->fireEvent(static::EVENT_GET_ACCESS_TOKEN)) {
            return $token;
        }

        // Retrieve a new access token from the API.
        $data = $this->getRawTokenData([
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->secret,
            'scope'         => 'resource-read resource-write',
        ]);

        if (! $data) {
            return null;
        }

        // Fire the access token event.
        $this->fireEvent(static::EVENT_SET_ACCESS_TOKEN, [
            'token-type'    => $data->token_type,
            'expires'       => $data->expires_in,
            'access-token'  => $token = $data->access_token,
        ]);

        return $token;
    }

    /**
     * @param  array  $params
     * @return \stdClass|null
     */
    private function getRawTokenData(array $params)
    {
        try {
            $response = $this->client->post('/oauth/token', [
                'form_params'   => $params,
                'on_stats'      => array($this, 'handleTransferStats'),
            ]);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (Exception $e) {
            return $this->handleGeneralException($e);
        }

        $data = json_decode((string) $response->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->addError(json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param  string $msg
     * @return null
     */
    protected function addError($msg)
    {
        $this->errors[] = $msg;

        return null;
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
     * @param \GuzzleHttp\TransferStats $stats
     */
    public function handleTransferStats(TransferStats $stats)
    {
        $this->fireEvent(static::EVENT_RESPONSE, [$stats]);
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
     * @param  \GuzzleHttp\Exception\ClientException $exception
     * @return null
     */
    protected function handleClientException(ClientException $exception)
    {
        return $this->handleException(static::EVENT_CLIENT_EXCEPTION, $exception);
    }

    /**
     * @param  \Exception $exception
     * @return null
     */
    protected function handleGeneralException(Exception $exception)
    {
        return $this->handleException(static::EVENT_EXCEPTION, $exception);
    }

    /**
     * @param  string     $event
     * @param  \Exception $exception
     * @return null
     * @throws \Exception
     */
    protected function handleException($event, Exception $exception)
    {
        // Default behaviour if no handlers are defined
        if (! isset($this->events[$event]) || ! $this->events[$event]) {
            if ($this->debug === true) {
                throw $exception;
            } else {
                return $this->addError($exception->getMessage());
            }
        }

        // TODO: pass in `$this->addError` as an callable argument
        $this->fireEvent($event, [$exception]);

        return null;
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

            // TODO: pass in the current `$result` as the first argument
            $result = call_user_func_array($callable, $arguments);
        }

        return $result;
    }
}
