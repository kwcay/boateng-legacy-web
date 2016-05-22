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
     * Exportable resources.
     *
     * @var array
     */
    protected $supportedResources = [
        'alphabet',
        'country',
        'definition',
        'language'
    ];

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

    }

    /**
     * Exports a resource to file.
     *
     * @param string $type
     * @param string $format
     * @throws \Exception
     */
    public function exportResource($type, $format)
    {
        // Quick check.
        if (!in_array($type, $this->supportedResources)) {
            throw new Exception('Invalid Resource Type.');
        }

        $className = 'App\\Models\\'. ucfirst($type);

        // Double-check data format.
        if (!in_array($format, $className::getExportFormats())) {
            throw new Exception('Invalid Format.');
        }

        // Format the data to be exported.
        $data = $className::withTrashed()->get();
        $export = [
            'meta' => [
                'type' => $type,
                'total' => count($data),
                'schema' => 'dinkomo-0.2',
            ],
            'data' => []
        ];

        foreach ($data as $resource) {
            $export['data'][] = array_except($resource->getExportArray(), ['id']);
        }

        $export['meta']['checksum'] = md5(json_encode($export['data']));

        // dd($export);

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
