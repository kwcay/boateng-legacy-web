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
                $result = Yaml::dump($data);
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

    public static function getExportFileName($format = '') {
        $className = explode('\\', get_called_class());
        return 'Di Nkomo_'. date('Y-m-d') .'_'. array_pop($className) .'s'. (strlen($format) ? '.'. $format : '');
    }
}

