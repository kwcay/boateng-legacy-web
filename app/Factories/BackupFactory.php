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
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->storage = Storage::disk('backups');
        $this->tempStorage = Storage::disk('backups-build');
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

        // Make sure a file with the same name doesn't already exist.
        if ($this->storage->exists($filename))
        {
            throw new Exception('Backup file already exists.');
        }

        // Upload file to backups disk.
        $handle = fopen($file->getRealPath(), 'r');
        // if (!$this->storage->put($filename, file_get_contents($file->getRealPath())))
        if (!$this->storage->put($filename, $handle))
        {
            fclose($handle);
            throw new Exception('Could not upload backup file.');
        }

        fclose($handle);
        $this->setMessage('Backup file successfully uploaded.');

        return $this;
    }

    /**
     * Restores a backup file.
     *
     * @param   string  $filename
     * @param   int     $timestamp
     */
    public function import($filename, $timestamp = null)
    {
        // Delete backup file.
        $this->setMessage('TODO: restore "'. $this->getPath($filename, $timestamp) .'"');

        return $this;
    }

    /**
     * Deletes a backup file.
     *
     * @param   string  $filename
     * @param   int     $timestamp
     */
    public function delete($filename, $timestamp = null)
    {
        // Delete backup file.
        $this->setMessage('TODO: delete "'. $this->getPath($filename, $timestamp) .'"');

        return $this;
    }

    /**
     * Retrieves the relative path of a backup file.
     *
     * @param   string  $filename
     * @param   int     $timestamp
     *
     * @throws  Exception
     */
    public function getPath($filename, $timestamp = null)
    {
        $fullName = null;

        // If a timestamp was provided, look for that specific backup.
        if ($timestamp > 0)
        {
            $fullName = date('Y-m', $timestamp) .'/'. $filename;

            if (!$this->storage->exists($fullName))
            {
                throw new Exception('Timestamp-filename combination does not exist.');
            }
        }

        // If none was provided, look for the latest backup matching the filename.
        else
        {
            $files = $this->storage->allFiles('/');

            foreach ($files as $file)
            {
                if (strpos($file, $filename) !== false) {
                    $fullName = $file;
                }
            }
        }

        // Make sure a file was found.
        if (!$fullName) {
            throw new Exception('Backup file not found.');
        }

        return $fullName;
    }
}
