<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Console\Commands;

use Phar;
use Storage;
use PharData;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class RestoreBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {file? : The relative path to the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores a backup file.';

    /**
     * Unique id for this restore process.
     *
     * @var string
     */
    protected $restoreId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->storage = Storage::disk('backups');
        $this->tempStorage = Storage::disk('backups-build');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Retrieve backup filename.
        $filename = $this->argument('file');

        if (empty($filename))
        {
            $files = array_reverse($this->storage->allFiles('/'));
            $filename = $this->choice('Select a backup file to restore:', $files, 0);
        }

        // Performance check.
        if (!$this->storage->exists($filename))
        {
            $this->error('Can\'t find backup file "'. $filename .'".');
            return 0;
        }

        // Confirm backup restore.
        if (!$this->confirm('Are you sure you want to restore the backup file "'. $filename .'"?')) {
            return 0;
        }

        // Create temporary directory to unpack files into.
        $this->restoreId = 'restore-'. date('Ymd') .'-'. substr(md5(microtime()), 20);
        if (!$this->tempStorage->makeDirectory($this->restoreId))
        {
            $this->error('Could not create temp directory to unpack backup file.');
            return 0;
        }

        // Copy backup file to temporary directory.
        if (!$this->tempStorage->put($this->restoreId .'/data.tar.gz', $this->storage->get($filename)))
        {
            $this->error('Could not copy backup file to temp directory.');
            return 0;
        }

        // Extract backup file.
        $this->info('Reading backup file...');
        $phar = new PharData($this->getDirName() .'/data.tar.gz');
        try
        {
            $phar->decompress();
            $phar->extractTo($this->getDirName());
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return 0;
        }

        // Retrieve meta data.
        $meta = $phar->getMetaData();
        if (empty($meta))
        {
            if (!$this->tempStorage->exists($this->restoreId .'/meta.yaml'))
            {
                $this->error('Could not find metadata for backup file.');
                $this->tempStorage->deleteDirectory($this->restoreId);
                return 0;
            }

            $meta = Yaml::parse($this->tempStorage->get($this->restoreId .'/meta.yaml'));
        }

        print_r($meta);

        // Remove phar files.
        $this->tempStorage->delete($this->restoreId .'/data.tar');
        $this->tempStorage->delete($this->restoreId .'/data.tar.gz');
    }

    /**
     * Retrieves the full path to the current temporary backup folder.
     *
     * @return string
     */
    protected function getDirName() {
        return config('filesystems.disks.backups-build.root') .'/'. $this->restoreId;
    }
}
