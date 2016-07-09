<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Console\Commands;

use Storage;
use Exception;
use Illuminate\Console\Command;
use App\Factories\BackupFactory;

class RestoreBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore
                            {file? : The relative path to the backup file}
                            {--R|refresh-db : Refresh migrations before restoring the backup}';

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
    public function __construct(BackupFactory $factory)
    {
        parent::__construct();

        $this->factory = $factory;
        $this->storage = Storage::disk('backups');
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

        // Restore backup file.
        $this->info('Reading backup file...');
        try
        {
            $this->factory->restore($filename, [
                'refresh-db' => ($this->option('refresh-db'))
            ]);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return 0;
        }

        $this->info('The backup file "'. $filename .'" has been restored.');
    }
}
