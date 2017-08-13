@setup

    # Initial setup

    require __DIR__.'/vendor/autoload.php';
    (new \Dotenv\Dotenv(__DIR__, '.env'))->load();
    use DoraBoateng\Deployer\Output as Out;

    # Configuration

    $gitHost            = env('ENVOY_GIT_HOST', 'deployer');
    $repository         = "git@{$gitHost}:doraboateng/web.git";
    $baseDir            = env('ENVOY_BASE_DIR', '/var/www/apps');
    $releasesDir        = "{$baseDir}/releases";
    $liveDir            = env('ENVOY_LIVE_DIR', '/var/www/live');
    $newReleaseName     = date('Ymd-His');
    $localDir           = dirname(__FILE__);
    $productionServer   = env('ENVOY_PRODUCTION', '127.0.0.1');
    $localServer        = env('ENVOY_LOCAL', '127.0.0.1');

@endsetup

@servers(['local' => $localServer, 'production' => $productionServer])



{{-- Zero downtime deployment --}}

{{-- Credits: https://serversforhackers.com/video/deploying-with-envoy-cast --}}
{{-- Credits: https://dyrynda.com.au/blog/an-envoyer-like-deployment-script-using-envoy --}}
{{-- Credits: https://murze.be/2015/11/zero-downtime-deployments-with-envoy --}}

@story('deploy', ['on' => 'production'])

    unit-tests
    git-clone
    setup-app
    install-dependencies
    update-permissions
    optimize
    update-symlinks
    purge-releases

@endstory



{{-- Helper tasks --}}

@task('check-prod', ['on' => 'production'])

    {{ Out::green('Checking production server...') }}
    ssh -T git@<?= $gitHost ?>

@endtask

@task('unit-tests', ['on' => 'local'])

    {{ Out::green('Running unit tests...') }}
    {{ Out::yellow('To do: run tests from Envoy and quit on fail') }}

@endtask

@task('git-clone')

    {{ Out::green('Cloning git repository...') }}

    # Check if the release directory exists. If it doesn't, create one.
    [ -d {{ $releasesDir }} ] || mkdir -p {{ $releasesDir }};

    # cd into the releases directory.
    cd {{ $releasesDir }};

    # Clone the repository into a new folder.
    git clone --depth 1 {{ $repository }} {{ $newReleaseName }}  &> /dev/null;

    # Configure sparse checkout.
    cd {{ $newReleaseName }};
    git config core.sparsecheckout true;
    echo "*" > .git/info/sparse-checkout;
    echo "!storage" >> .git/info/sparse-checkout;
    git read-tree -mu HEAD;

@endtask

@task('setup-app')

    {{ Out::green('Copying environment file...') }}

    # cd into new folder.
    cd {{ $releasesDir }}/{{ $newReleaseName }};

    # Copy .env file
    cp -f ./.env.production ./.env;

@endtask

@task('install-dependencies')

    {{ Out::green('Installing composer dependencies...') }}

    # cd into new folder.
    cd {{ $releasesDir }}/{{ $newReleaseName }};

    # Install composer dependencies.
    {{ Out::yellow('NOTE: composer self-update requires root access') }}
    #composer self-update &> /dev/null;
    composer install --prefer-dist --no-scripts --no-dev -q -o &> /dev/null;

    # Update npm dependencies.
    {{ Out::green('Installing node dependencies...') }}
    npm install --production &> /dev/null

    # Build front-end assets.
    {{ Out::green('Building frontend assets...') }}
    {{ Out::yellow('TODO: weigh pros and cons of building assets in prod') }}
    gulp --production &> /dev/null

@endtask

@task('update-permissions')

    {{ Out::green('Updating directory owner and permissions...') }}

    # cd into releases folder
    cd {{ $releasesDir }};

    # Update group owner and permissions
    chgrp -R www-data {{ $newReleaseName }};
    chmod -R ug+rwx {{ $newReleaseName }};
    chmod -R 1777 {{ $newReleaseName }}/storage;

@endtask

@task('update-symlinks')

    {{ Out::green('Updating symbolic links...') }}

    # Make sure the persistent storage directory exists.
    #[ -d {{ $baseDir }}/storage ] || mkdir -p {{ $baseDir }}/storage;
    mkdir -p {{ $baseDir }}/storage/app;
    mkdir -p {{ $baseDir }}/storage/framework/sessions;
    mkdir -p {{ $baseDir }}/storage/framework/views;
    mkdir -p {{ $baseDir }}/storage/logs;

    # Remove the storage directory and replace with persistent data
    rm -rf {{ $releasesDir }}/{{ $newReleaseName }}/storage;
    cd {{ $releasesDir }}/{{ $newReleaseName }};
    ln -nfs {{ $baseDir }}/storage storage;

    ln -nfs {{ $releasesDir }}/{{ $newReleaseName }} {{ $liveDir }};
    chgrp -h www-data {{ $liveDir }};

@endtask

@task('optimize', ['on' => 'production'])

    {{ Out::green('Optimizing...') }}

    cd {{ $liveDir }};

    # Optimize installation.
    php artisan cache:clear;
    php artisan clear-compiled;
    php artisan optimize;
    php artisan config:cache;
    # php artisan route:cache;

    # Clear the OPCache
    {{ Out::white('Clearing OPCache...') }}
    {{ Out::blue('TODO: requires root access') }}
    # sudo service php5-fpm restart

@endtask

@task('purge-releases', ['on' => 'production'])

    {{ Out::green('Purging old releases...') }}

    # This will list our releases by modification time and delete all but the 5 most recent.
    purging=$(ls -dt {{ $releasesDir }}/* | tail -n +5);

    if [ "$purging" != "" ]; then
        echo Purging old releases: $purging;
        rm -rf $purging;
    else
        echo "No releases found for purging at this time";
    fi

@endtask



{{-- Testing Envoy --}}

@story('test')

    {{ Out::green('Testing Envoy locally...') }}

    ls

    {{ Out::purple('Ok') }}

    check-prod

@endstory
