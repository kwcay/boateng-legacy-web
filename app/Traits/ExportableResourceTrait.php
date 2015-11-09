<?php namespace App\Traits;

use Symfony\Component\Yaml\Yaml;

trait ExportableResourceTrait
{
    public static $contentTypes = [
        'json' => 'application/json',
        'yaml' => 'text/x-yaml',
        'yml' => 'text/x-yaml'
    ];

    /**
     * Returns an array of attributes that includes some hidden properties.
     */
    public function getExportArray()
    {
        // Temporarily disable hidden fields.
        $originallyHidden = $this->hidden;
        $this->hidden = array_where($this->hidden, function($key, $value) {
            return !in_array($value, ['params', 'created_at', 'deleted_at']);
        });

        // Retrieve attributes and relations.
        $attributes = $this->attributesToArray();

        // Reset hidden fields.
        $this->hidden = $originallyHidden;

        return $attributes;
    }

    /**
     * @param $data
     * @param string $format
     * @return mixed|string
     * @throws \Exception
     */
    public static function export($data, $format = 'yaml')
    {
        switch ($format)
        {
            case 'json':
                $result = json_encode($data);
                break;

            case 'yml':
            case 'yaml':
                $result = Yaml::dump($data, 4);
                break;

            default:
                throw new \Exception('Invalid export format.');
        }

        return $result;
    }

    public static function getExportFormats() {
        $static = new static;
        return isset($static->exportFormats) ? $static->exportFormats : ['yml', 'yaml', 'json'];
    }

    public static function getContentType($format) {
        return isset(static::$contentTypes[$format]) ? static::$contentTypes[$format] : 'text/plain';
    }

    public static function getExportFileName($format = '')
    {
        // Header name.
        $className = explode('\\', get_called_class());
        $name = 'Di Nkomo '. array_pop($className);

        // Unique name.
        $unique = date('Y-m-d') .'_'. substr(sha1(microtime()), 0, 8);

        // File extension.
        $extension = strlen($format) ? '.'. $format : '';

        return $name .'_'. $unique . $extension;
    }

    /**
     * Converts the model instance to an array, and changes the snake_case keys to camelCase.
     *
     * @return array
     */
    public function toArray()
    {
        $camelCasedAttributes = [];
        $snakeCaseAttributes = parent::toArray();

        foreach ($snakeCaseAttributes as $key => $value) {
            $camelCasedAttributes[camel_case($key)] = $value;
        }

        return $camelCasedAttributes;
    }
}
