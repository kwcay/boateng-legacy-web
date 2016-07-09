<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories;

use Phar;
use Artisan;
use Storage;
use PharData;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;
use App\Factories\DataImportFactory;
use Illuminate\Http\UploadedFile as File;
use App\Factories\Factory as FactoryContract;

/**
 *
 */
class BackupFactory extends FactoryContract
{
    /**
     * Specifies the number of objects to store per file for each resource. When restoring a
     * backup file, resources will be loaded in the order specified here as well.
     *
     * @var array
     */
    protected $resourceLimits = [
        'country'   => 500,
        'reference' => 1000,
        'alphabet'  => 500,
        'language'  => 500,
        'culture'   => 500,
        'definition' => 200,
        'user'      => 500,
    ];

    /**
    * @param Illuminate\Http\Request $request
     */
    public function __construct(Request $request, DataImportFactory $importHelper)
    {
        $this->request = $request;
        $this->importHelper = $importHelper;

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
     * @param   array   $options
     * @return  void
     *
     * @throws  Exception
     */
    public function restore($filename, array $options = [])
    {
        // Performance check.
        $file = $this->getPath($filename);
        if (!$this->storage->exists($file)) {
            throw new Exception('Can\'t find backup file "'. $file .'"');
        }

        // Run some pre-restore checks & tasks.
        Artisan::call('down');

        // Unpack backup file.
        $restoreId = 'restore-'. date('Ymd') .'-'. substr(md5(microtime()), 20);
        if (!$this->tempStorage->makeDirectory($restoreId))
        {
            throw new Exception('Could not create temp directory to unpack backup file.');
        }

        if (!$this->tempStorage->put($restoreId .'/data.tar.gz', $this->storage->get($file)))
        {
            $this->tempStorage->deleteDirectory($restoreId);

            throw new Exception('Could not copy backup file to temp directory.');
        }

        // Extract backup file.
        $phar = new PharData($this->getDirName($restoreId) .'/data.tar.gz');
        try
        {
            $phar->decompress();
            $phar->extractTo($this->getDirName($restoreId));
        }
        catch (Exception $e)
        {
            unset($phar);
            Phar::unlinkArchive($this->getDirName($restoreId) .'/data.tar.gz');
            $this->tempStorage->deleteDirectory($restoreId);

            throw new Exception($e->getMessage());
        }

        // Retrieve meta data.
        $meta = $phar->getMetaData();
        if (empty($meta))
        {
            if (!$this->tempStorage->exists($restoreId .'/meta.yaml'))
            {
                unset($phar);
                Phar::unlinkArchive($this->getDirName($restoreId) .'/data.tar');
                Phar::unlinkArchive($this->getDirName($restoreId) .'/data.tar.gz');
                $this->tempStorage->deleteDirectory($restoreId);

                throw new Exception('Could not find metadata for backup file.');
            }

            $meta = Yaml::parse($this->tempStorage->get($restoreId .'/meta.yaml'));
        }

        // Remove phar files.
        unset($phar);
        Phar::unlinkArchive($this->getDirName($restoreId) .'/data.tar');
        Phar::unlinkArchive($this->getDirName($restoreId) .'/data.tar.gz');

        // Refresh database.
        if (isset($options['refresh-db']))
        {
            print "Refreshing migrations...\n";
            Artisan::call('migrate:refresh');
        }


        // Restore backup.
        $this->importHelper->setDataMeta($meta);
        foreach ($this->resourceLimits as $resource => $limit)
        {
            // Performance check.
            if (!isset($meta[$resource]) || $meta[$resource]['files'] < 1) {
                continue;
            }

            print "Loading {$meta[$resource]['files']} {$resource} files...\n";

            // Setup our DataImportFactory.
            $this->importHelper->setDataModel('App\\Models\\'. ucfirst($resource));


            // Loop through each file and import data using the DataImportFactory.
            for ($i = 0; $i < $meta[$resource]['files']; $i++)
            {
                $dataFile = "{$restoreId}/{$resource}-{$i}.{$meta['format']}";
                if (!$this->tempStorage->exists($dataFile)) {
                    continue;
                }

                // Retrieve raw data.
                $data = $this->tempStorage->get($dataFile);
                $data = $meta['format'] == 'yaml' ? Yaml::parse($data) : json_decode($data, true);

                // File checksum.
                if (!isset($options['skipChecksum']) || !$options['skipChecksum'])
                {
                    if ($meta[$resource]['checksums'][$i] !== $this->checksum($data, $meta['checksum-method']))
                    {
                        $this->tempStorage->deleteDirectory($restoreId);

                        throw new Exception(
                            "Checksum failed for {$resource}-{$i}.{$meta['format']} (expected \"".
                            $meta[$resource]['checksums'][$i] .'", got "'.
                            $this->checksum($data, $meta['checksum-method']) .'")'
                        );
                    }
                }

                // Import data.
                try
                {
                    $this->importHelper->setDataArray($data);
                    $results = $this->importHelper->importDataSet();

                    foreach ($results->getMessages() as $msg)
                    {
                        print "[$i] {$msg}\n";
                    }
                }
                catch (Exception $e)
                {
                    $this->tempStorage->deleteDirectory($restoreId);

                    throw new Exception($e->getMessage());
                }
            }
        }

        // Delete temporary folder.
        $this->tempStorage->deleteDirectory($restoreId);

        // Last checks & tasks.
        Artisan::call('up');

        return $this;
    }

    /**
     * Deletes a backup file.
     *
     * @param   string  $filename
     * @param   int     $timestamp
     *
     * @todo    Restrict access based on roles.
     */
    public function delete($filename, $timestamp = null)
    {
        // Delete backup file.
        if (!$this->storage->delete($this->getPath($filename, $timestamp)))
        {
            throw new Exception('Couldn\'t delete backup file "'. $this->getPath($filename, $timestamp) .'".');
        }

        $this->setMessage('Backup file successfully deleted.');

        return $this;
    }

    /**
     * Calculates a checksum.
     */
    public function checksum($data, $method = 'json-md5')
    {
        return md5(json_encode($data));
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

    /**
     * Retrieves the full path to a temporary directory.
     *
     * @return string
     */
    protected function getDirName($folder = '') {
        return config('filesystems.disks.backups-build.root') .'/'. $folder;
    }
}
