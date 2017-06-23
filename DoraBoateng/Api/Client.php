<?php

namespace DoraBoateng\Api;

use Sentry;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class Client
{
    const API_VERSION               = '0.4';

    const ERROR_INVALID_ID          = 10;
    const ERROR_INVALID_CODE        = 11;
    const ERROR_INVALID_QUERY       = 12;
    const ERROR_INVALID_RESOURCE    = 13;

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
     *
     */
    protected $cache;

    /**
     * @var array
     */
    protected $events = [
        'get-token' => [],
        'set-token' => [],
    ];

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
            // TODO: throw proper error
            throw new \Exception('Invalid API Credentials.');
        }

        $this->clientId = $config['id'];
        $this->secret = $config['secret'];

        // TODO: save reference to cache, or callback to handle caching?
        $this->cache = $config['temp_cache'];

        // Configure base URI
        $endpoint = isset($config['api_host'])
            ? $config['api_host']
            : 'https://api.doraboateng.com';
        $endpoint .= '/'.static::API_VERSION.'/';

        $config['base_uri'] = $endpoint;

        $this->client = new GuzzleClient(array_except($config, ['api_host', 'id', 'secret']));
    }

    /**
     * Retrieves a definition.
     *
     * @param  int   $id     Definition ID
     * @param  array $embed  Relations to include with definition
     * @return stdClass
     */
    public function getDefinition($id, array $embed = [])
    {
        if (! $this->isValidId($id)) {
            return static::ERROR_INVALID_ID;
        }

        return $this->get('definitions/'.$id, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     * Searches the API for definitions.
     *
     * @param string $query
     * @param string $langCode
     * @return stdClass|int
     */
    public function searchDefinitions($query, $langCode = null)
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            return self::ERROR_INVALID_QUERY;
        }

        if ($langCode && ! $this->isValidLanguageCode($langCode)) {
            return self::ERROR_INVALID_ID;
        }

        return $this->get('definitions/search/'.urlencode($query), [
            'lang' => $langCode,
        ]);
    }

    /**
     * @param int|string $langId
     * @param array $embed
     */
    public function getRandomDefinition($langId = null, array $embed = [])
    {
        if ($langId) {
            if (is_numeric($langId) && ! $this->isValidId($langId)) {
                return static::ERROR_INVALID_ID;
            } elseif (! $this->isValidLanguageCode($langId)) {
                return static::ERROR_INVALID_CODE;
            }
        } else {
            $langId = '';
        }

        return $this->get('definitions/random/'.$langId, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     *
     */
    public function getLanguage($id, array $embed = [])
    {
        if (! $this->isValidId($id) && ! $this->isValidLanguageCode($id)) {
            return self::ERROR_INVALID_ID;
        }

        return $this->get('languages/'.$id, [
            'embed' => implode(',', $embed)
        ]);
    }

    /**
     *
     */
    public function getLanguageOfTheWeek(array $embed = [])
    {
        return $this->get('languages/weekly', [
            'embed' => $embed ? implode(',', $embed) : null
        ]);
    }

    /**
     * @param string $query
     */
    public function search($query)
    {
        $query = trim($query);

        if (strlen($query) < 1) {
            return self::ERROR_INVALID_QUERY;
        }

        return $this->get('search/'.$query);
    }

    /**
     * @deprecated
     */
    public function apiGet($endpoint, array $query = [])
    {
        return $this->get($endpoint, $query);
    }

    /**
     *
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
        if (! $token = $this->cache->get('doraboateng.accesstoken')) {
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
                throw new \Exception('JSON Error: '. json_last_error_msg());
            }

            $this->cache->put('doraboateng.accesstoken', $token = $data->access_token, $data->expires_in / 60);
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
}
