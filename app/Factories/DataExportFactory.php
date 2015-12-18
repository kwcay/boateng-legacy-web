<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories;

use Exception;
use Symfony\Component\Yaml\Yaml;
use App\Factories\Contract as BaseFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;

class DataExportFactory extends BaseFactory
{
    /**
     * Supported export formats.
     */
    protected $exportFormats = [
        'Definition' => ['yml', 'yaml', 'json', 'bgl', 'dict'],
        'Language' => ['yml', 'yaml', 'json'],
    ];

    /**
     * Called once class has been instantiated.
     */
    public function boot()
    {
        $this->setDataPath(storage_path() .'/app/export');
    }

    /**
     * Exports a resource to file.
     *
     * @throws \Exception
     * @param string $type
     * @param string $format
     */
    public function exportResource($type, $format)
    {
        // Quick check.
        if (!in_array($type, ['language', 'definition'])) {
            throw new Exception('Invalid Resource Type.');
        }

        $className = 'App\\Models\\'. ucfirst($resourceType);

        // Double-check data format.
        if (!in_array($format, $className::getExportFormats())) {
            throw new Exception('Invalid Format.');
        }

        // Format the data to be exported.
        $data = $className::all();
        $export = [
            'meta' => [
                'type' => $type,
                'total' => count($data)
            ],
            'data' => []
        ];

        foreach ($data as $resource) {
            $export['data'][] = array_except($resource->getExportArray(), ['id']);
        }

        $export['meta']['checksum'] = md5(json_encode($export['data']));

        return [
            'Content-Type' => $className::getContentType($format),
            'filename' => $className::getExportFileName($format),
            'content' => $className::export($export, $format)
        ];
    }

    /**
     * Starts a backup task.
     *
     * @return string   Some kind of identifier (e.g. ZIP filename) for backup task.
     */
    public function runBackupTask()
    {
        throw new Exception('DataExportFactory::runBackupTask() Not Implemented', 501);
    }
}
