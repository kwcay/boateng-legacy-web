<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Console\Commands;

use Phar;
use Storage;
use PharData;
use App\Models\Tag;
use App\Models\User;
use App\Models\Country;
use App\Models\Alphabet;
use App\Models\Language;
use App\Models\Reference;
use App\Models\Definition;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup {--Y|yaml : Export internal files as YAML (instead of JSON)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a backup of our data.';

    /**
     * Final output format of backup file.
     *
     * @var string
     */
    protected $format = 'nko';

    /**
     * Metadata related to current backup.
     *
     * @var array
     */
    protected $meta = [
        'id' => '',
    ];

    /**
     * Max # of resources to store per file.
     *
     * @var array
     */
    protected $limits = [
        'alphabet'  => 500,
        'country'   => 500,
        'definition' => 200,
        'language'  => 500,
        'reference' => 1000,
        'tag'       => 1000,
        'user'      => 500,
    ];

    /**
     * The progress bar shows the progress of the backup process.
     *
     * @var Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar;

    /**
     * The storage disk is used to store backup files.
     *
     * @var Illuminate\Filesystem\FilesystemAdapter
     */
    protected $storage;

    /**
     * The temporary storage disk is used to build backup files.
     *
     * @var Illuminate\Filesystem\FilesystemAdapter
     */
    protected $tempStorage;

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
        // Generate a unique backup name.
        $this->meta['id'] = 'Di_Nkomo_'. date('Ymd') .'-'. substr(md5(microtime()), 20);

        // Store the default internal data format.
        $this->meta['format'] = $this->option('yaml') ? 'yaml' : 'json';

        $this->info("Backing up to {$this->meta['id']}.{$this->format}");

        // Calculate # of steps to complete backup.
        // Step 1: Start.
        // Step 2: Create temporary backup directory.
        // Steps 3 to n: create dump files.
        // Step n+1: Create meta file.
        // Step n+2: Finalize backup file.
        // Step n+3: Copy backgup file to backups disk.
        // Step n+4: Cleanup.
        $steps = 6;
        foreach ($this->limits as $resource => $limit)
        {
            $className = 'App\\Models\\'. ucfirst($resource);

            $this->meta[$resource] = (int) ceil($className::count() / $limit);
            $steps += $this->meta[$resource];
        }

        print_r($this->meta);

        $this->progressBar = $this->output->createProgressBar($steps);
        $this->progressBar->advance();

        // Create backup folder.
        $this->tempStorage->makeDirectory($this->meta['id']);
        $this->progressBar->advance();

        // Start dumping resources.
        foreach ($this->limits as $resource => $limit)
        {
            // Performance check.
            if ($this->meta[$resource] < 1) {
                continue;
            }

            // We will split the resource data into separate files, depending on the specified
            // limits. Using "$className::withTrashed()->chunk()" somehow isn't helpful here,
            // so we will chunk the data manually.
            for ($i = 0; $i < $this->meta[$resource]; $i++)
            {
                $dump = [];
                $skip = $i * $limit;
                $className = 'App\\Models\\'. ucfirst($resource);
                $models = $className::withTrashed()->skip($skip)->take($limit)->get();

                foreach ($models as $model) {
                    $dump[] = $model->getExportArray();
                }

                $this->createFile("{$resource}.{$i}", $dump);

                // Save the md5 checksum of the json-encoded data.
                $this->meta["{$resource}-json-md5-{$i}"] = md5(json_encode($dump));

                $this->progressBar->advance();
            }
        }

        // Create meta file.
        $this->createFile('meta.yaml', Yaml::dump($this->meta));
        $this->progressBar->advance();

        // tar & gzip folder
        $phar = new PharData($this->getDirName() .'/'. $this->meta['id'] .'.tar');
        $phar->buildFromDirectory($this->getDirName());
        $phar->setMetadata($this->meta);
        $phar->compress(Phar::GZ);
        $this->progressBar->advance();

        // Copy backup file to backups disk.
        $this->storage->put(
            date('Y-m') .'/'. $this->meta['id'] .'.nko',
            $this->tempStorage->get($this->meta['id'] .'/'. $this->meta['id'] .'.tar.gz')
        );
        $this->progressBar->advance();

        // Remove temporary files.
        $this->tempStorage->deleteDirectory($this->meta['id']);
        $this->progressBar->advance();
    }

    /**
     * Creates a file in the current temporary backup folder.
     *
     * @param string $filename
     * @param mixed $contents
     * @return ?
     */
    protected function createFile($filename, $contents)
    {
        // Serialize file data.
        if (!is_string($contents))
        {
            $contents = $this->option('yaml') ? Yaml::dump($contents) : json_encode($contents);
        }

        return $this->tempStorage->put($this->meta['id'] .'/'. $filename, $contents);
    }

    /**
     * Retrieves the full path to the current temporary backup folder.
     *
     * @return string
     */
    protected function getDirName() {
        return config('filesystems.disks.backups-build.root') .'/'. $this->meta['id'];
    }
}
