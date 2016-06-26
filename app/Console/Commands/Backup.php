<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Console\Commands;

use App\Models\Tag;
use App\Models\User;
use App\Models\Country;
use App\Models\Alphabet;
use App\Models\Language;
use App\Models\Reference;
use App\Models\Definition;
use Illuminate\Console\Command;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a backup of our data.';

    /**
     * Backup name.
     *
     * @var string
     */
    protected $backupName;

    /**
     * Progress bar.
     *
     * @var
     */
    protected $progressBar;

    /**
     * Number of alphabets to store per file.
     *
     * @var int
     */
    protected $alphabetLimit = 500;

    /**
     * Number of countries to store per file.
     *
     * @var int
     */
    protected $countryLimit = 500;

    /**
     * Number of definitions to store per file.
     *
     * @var int
     */
    protected $definitionLimit = 300;

    /**
     * Number of languages to store per file.
     *
     * @var int
     */
    protected $languageLimit = 500;

    /**
     * Number of references to store per file.
     *
     * @var int
     */
    protected $referenceLimit = 1000;

    /**
     * Number of tags to store per file.
     *
     * @var int
     */
    protected $tagLimit = 1000;

    /**
     * Number of users to store per file.
     *
     * @var int
     */
    protected $userLimit = 500;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Setup backup tasks.
        $this->info('Preparing backup...');

        // Generate a unique backup name.
        $this->backupName = date('Ymd') .'-'. substr(md5(microtime()), 20);
        $this->comment('Backup name: '. $this->backupName);

        // Count # of files to create.
        $steps = 0;
        $steps += ceil(Alphabet::count() / $this->alphabetLimit);
        $steps += ceil(Country::count() / $this->countryLimit);
        $steps += ceil(Definition::count() / $this->definitionLimit);
        $steps += ceil(Language::count() / $this->languageLimit);
        $steps += ceil(Reference::count() / $this->referenceLimit);
        $steps += ceil(Tag::count() / $this->referenceLimit);
        $steps += ceil(User::count() / $this->userLimit);

        $this->comment("{$steps} steps to backup");

        // Create backup folder.
        // ...

        // Create meta file.
        // ...

        // Start dumping resources.
        // ...

        // tar & gzip folder, rename extension to .nko
        // TODO: find out if we can password-protect a .gz file.
        // ...

        // MD5 file contents, use checksum in filename.
        // ...
    }
}
