<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Console\Commands;

use Storage;
use Illuminate\Console\Command;

class ListBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists available backup files.';

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
        $files = array_reverse($this->storage->allFiles('/'));

        foreach ($files as $index => $filename)
        {
            $files[$index] = [
                $filename,
                number_format($this->storage->size($filename) / 1000) .' kb'
            ];
        }

        $this->table(['Filename', 'Size'], $files);
    }
}
