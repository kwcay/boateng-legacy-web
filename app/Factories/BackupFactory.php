<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories;

use Storage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as File;
use App\Factories\Factory as FactoryContract;

/**
 *
 */
class BackupFactory extends FactoryContract
{
    /**
    * @param Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->boot();
    }

    /**
     * Uploads a backup file.
     *
     * @param   Illuminate\Http\UploadedFile    $file
     * @return  App\Factories\BackupFactory
     *
     * @throws Exception
     */
    public function upload(File $file)
    {
        // Retrieve filename.
        $file = $this->request->file('file');
        $filename = date('Y-m') .'/'. $file->getClientOriginalName();
        $storage = Storage::disk('backups');

        // Make sure a file with the same name doesn't already exist.
        if ($storage->exists($filename))
        {
            throw new Exception('Backup file already exists.');
        }

        // Upload file to backups disk.
        if (!$storage->put($filename, file_get_contents($file->getRealPath())))
        {
            throw new Exception('Could not upload backup file.');
        }

        $this->setMessage('Backup file successfully uploaded.');

        return $this;
    }
}
