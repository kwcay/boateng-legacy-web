<?php

namespace App\Utilities;

class Asset
{
    /**
     * @const string
     */
    const MANIFEST_FILE = 'resources/assets/build/manifest.json';

    /**
     * @var array
     */
    private $manifest;

    /**
     * @param array|null $manifest
     */
    public function __construct (array $manifest = null)
    {
        $this->manifest = $manifest ?: $this->load(self::MANIFEST_FILE);
    }

    /**
     * Loads a JSON-formatted manifest file.
     *
     * @param  string $path
     * @return array
     */
    private function load(string $path) : array
    {
        if (! file_exists($path = base_path($path))) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?: [];
    }

    /**
     * Shortcut to retrieve the path to an asset file.
     *
     * @param  string $filename
     * @return string
     */
    public static function for(string $filename) : string
    {
        return static::instance()->file($filename) ?: '';
    }

    /**
     * @param  string $filename
     * @return mixed|null
     */
    public function file(string $filename)
    {
        return array_key_exists($filename, $this->manifest)
            ? $this->manifest[$filename]
            : null;
    }

    /**
     * @return static
     */
    public static function instance()
    {
        static $instance;

        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }
}
