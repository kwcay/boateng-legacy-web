<?php namespace App\Traits;

use Symfony\Component\Yaml\Yaml;

trait ExportableResourceTrait
{
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
}

