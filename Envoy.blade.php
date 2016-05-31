@setup

    # Load .env file.
    require __DIR__.'/vendor/autoload.php';
    (new \Dotenv\Dotenv(__DIR__, '.env'))->load();

    # Servers
    $local = env('ENVOY_LOCAL_SERVER', '127.0.0.1');
    $staging = env('ENVOY_STAGING_SERVER', '127.0.0.1');
    $production = env('ENVOY_PRODUCTION_SERVER', '127.0.0.1');

    # Other variables
    $localPath = env('ENVOY_LOCAL_PATH', '/var/www/');
    $stagingPath = env('ENVOY_STAGING_PATH', '/var/www/');
    $productionPath = env('ENVOY_PRODUCTION_PATH', '/var/www/');

    // $server = "";
    // $repository = "spatie/{$server}";
    // $baseDir = "/home/forge/{$server}";
    // $releasesDir = "{$baseDir}/releases";
    // $currentDir = "{$baseDir}/current";
    // $newReleaseName = date('Ymd-His');
    // $newReleaseDir = "{$releasesDir}/{$newReleaseName}";
    // $user = get_current_user();

    /**
     * Logs a message to the console.
     * Credits:
     *
     * @param string $message
     * @return string
     */
    function msg($message) {
        return "echo '\033[32m" . $message . "\033[0m';\n";
    }
@endsetup



{{-- Servers --}}
@servers(['local' => $local, 'staging' => $staging, 'production' => $production])



{{-- Zero downtime deployment --}}

@task('link', ['on' => 'web'])
    rm /var/www/jobable.com.au
    ln -s ~/jobable.com.au /var/www
@endtask



{{-- Regular deployment --}}

@macro('deploy')

@endmacro

@macro('deploy-staging')

@endmacro



{{-- Asset management --}}

@task('update', ['on' => 'local'])

    cd {{ $localPath }}

    {{ msg('Updating bower dependencies...') }}
    {{ msg('To do: update bower.') }}
    bower update &> /dev/null

    {{ msg('Updating node dependencies...') }}
    {{ msg('To do: update node.') }}
    npm update &> /dev/null

    {{ msg('Updating composer dependencies...') }}
    composer self-update &> /dev/null
    composer update &> /dev/null

@endtask

@task('install', ['on' => 'local'])

    cd {{ $localPath }}

    {{ msg('Installing bower dependencies...') }}
    {{ msg('To do: update bower.') }}
    bower install &> /dev/null

    {{ msg('Installing node dependencies...') }}
    {{ msg('To do: update node.') }}
    npm install &> /dev/null

@endtask

@task('composer-install-staging', ['on' => 'stage'])

    cd {{ $stagingPath }}

    {{ msg('Installing composer dependencies...') }}
    composer self-update &> /dev/null
    composer install &> /dev/null

@endtask

@task('composer-install-production', ['on' => 'production'])

    cd {{ $stagingPath }}

    {{ msg('Installing composer dependencies...') }}
    composer self-update &> /dev/null
    composer install &> /dev/null

@endtask

@task('build-assets', ['on' => 'local'])

    cd {{ $stagingPath }}

    {{ msg('Building assets...') }}
    gulp --production &> /dev/null
    gulp --production --back &> /dev/null

@endtask



{{-- Git --}}

@task('git-pull-staging', ['on' => 'staging'])

    cd {{ $stagingPath }}

    git config core.ignorecase false
    git reset --hard origin/master
    git pull
    git status

@endtask

@task('git-pull-production', ['on' => 'production'])

    cd {{ $productionPath }}

    git config core.ignorecase false
    git reset --hard origin/master
    git pull
    git status

@endtask



{{-- Database --}}

@task('migrate-staging', ['on' => 'staging'])

    cd {{ $stagingPath }}

    echo "yes" | php artisan migrate --force

@endtask

@task('migrate-production', ['on' => 'production'])

    cd {{ $productionPath }}

    echo "yes" | php artisan migrate --force

@endtask

@task('resetdb-staging', ['on' => 'staging'])

    cd {{ $stagingPath }}

    composer dump-autoload
    echo "yes" | php artisan migrate:reset --force

@endtask

@task('resetdb-production', ['on' => 'production'])

    cd {{ $productionPath }}

    composer dump-autoload
    echo "yes" | php artisan migrate:reset --force

@endtask



{{-- Test commands --}}

@task('local', ['on' => 'local'])

    {{ msg('Testing Envoy on localhost...') }}
    cd {{ $localPath }}
    ls

@endtask

@task('staging', ['on' => 'staging'])

    {{ msg('Testing Envoy on staging server...') }}
    cd {{ $stagingPath }}
    ls

@endtask

@task('production', ['on' => 'production'])

    {{ msg('Testing Envoy on production server...') }}
    cd {{ $productionPath }}
    ls

@endtask
