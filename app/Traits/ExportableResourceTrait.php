<?php namespace App\Traits;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Http\JsonResponse;

trait ExportableResourceTrait {

    /**
     * @param $data
     * @param string $format
     * @param bool $toFile
     * @return mixed|string
     * @throws \Exception
     */
    public static function exportToFormat($data, $format = 'yaml', $toFile = true)
    {
        switch ($format)
        {
            case 'json':
                $result = static::exportToJson($data);
                break;

            case 'yaml':
                $result = static::exportToYaml($data);
                $result = $toFile ? $result : '<pre>'. $result .'</pre>';
                break;

            default:
                throw new \Exception('Invalid export format.');
        }

        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function exportToJson($data) {
        return JsonResponse::create($data);
    }

    /**
     * @param $data
     * @return string
     */
    public static function exportToYaml($data) {
        return Yaml::dump($data);
    }
}
