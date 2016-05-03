<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Traits;

use Symfony\Component\Yaml\Yaml;

trait ExportableTrait
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
        $this->hidden = $this->hiddenFromExport ?: array_where($this->hidden, function($key, $value) {
            return !in_array($value, ['params', 'created_at', 'deleted_at']);
        });

        // Retrieve attributes and relations.
        $attributes = $this->attributesToArray();

        // Reset hidden fields.
        $this->hidden = $originallyHidden;

        return $attributes;
    }

    /**
     * Converts a resources into the specified format.
     *
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
                throw new \Exception('Invalid Format.');
        }

        return $result;
    }

    /**
     * Returns a list of supported export formats.
     */
    public static function getExportFormats() {
        $static = new static;
        return isset($static->exportFormats) ? $static->exportFormats : ['yml', 'yaml', 'js', 'json'];
    }

    /**
     * Generates a unique filename for the exported file.
     *
     * @param string $format
     * @return string
     */
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
     * Returns the content type for the specified format.
     *
     * @param string $format
     * @return string
     */
    public static function getContentType($format) {
        return isset(static::$contentTypes[$format]) ? static::$contentTypes[$format] : 'text/plain';
    }

    /**
     * Imports a resource into the database.
     *
     * @param array $data   Array of `static` resources.
     * @return array        Import results.
     */
    public static function import(array $data)
    {
        $results = [
            'total' => count($data),
            'imported' => 0,
            'skipped' => 0
        ];

        foreach ($data as $resource)
        {
            // Performance check.
            if (!$resource instanceof static) {
                $results['skipped']++;
                continue;
            }

            // Validate.
            $test = static::validate($resource->getArrayableAttributes());
            if ($test->fails()) {
                $results['skipped']++;
                continue;
            }

            // Import resource.
            $resource->save() ? $results['imported']++ : $results['skipped']++;
        }

        return $results;
    }
}
