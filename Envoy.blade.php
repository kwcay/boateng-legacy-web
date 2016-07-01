@setup

    # Load .env file.
    require __DIR__.'/vendor/autoload.php';
    (new \Dotenv\Dotenv(__DIR__, '.env'))->load();

    # Setup variables.
    $repository = "git@bitbucket.org:dinkomo/web.git";
    $baseDir = "/var/www/html/apps/dinkomo-web";
    $releasesDir = "{$baseDir}/releases";
    $liveDir = "/var/www/html/live/dinkomo-web";
    $newReleaseName = date('Ymd-His');

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

@servers(['local' => '127.0.0.1', 'production' => 'root@45.55.60.14'])



{{-- Zero downtime deployment --}}

{{-- Credits: https://serversforhackers.com/video/deploying-with-envoy-cast --}}
{{-- Credits: https://dyrynda.com.au/blog/an-envoyer-like-deployment-script-using-envoy --}}
{{-- Credits: https://murze.be/2015/11/zero-downtime-deployments-with-envoy --}}



@macro('deploy', ['on' => 'production'])

    git-clone
    setup-app
    composer-install
    update-permissions
    update-symlinks
    optimize
    purge-releases

@endmacro

@macro('deploy-migrate', ['on' => 'production'])

    git-clone
    setup-app
    composer-install
    update-permissions
    update-symlinks
    down
    migrate
    up
    optimize
    purge-releases

@endmacro

@task('deploy-code', ['on' => 'production'])

    cd {{ $liveDir }}
    git pull origin master

@endtask

@task('git-clone')

    {{ msg('Cloning git repository...') }}

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

    {{ msg('Setting up app...') }}

    # cd into new folder.
    cd {{ $releasesDir }}/{{ $newReleaseName }};

    # Copy .env file
    cp -f ./.env.production ./.env;

@endtask

@task('composer-install')

    {{ msg('Installing composer dependencies...') }}

    # cd into new folder.
    cd {{ $releasesDir }}/{{ $newReleaseName }};

    # Install composer dependencies.
    composer self-update &> /dev/null;
    composer install --prefer-dist --no-scripts -q -o &> /dev/null;

@endtask

@task('composer-update')

    {{ msg('Updating composer dependencies...') }}

    # cd into live folder.
    cd {{ $liveDir }};

    # Update composer dependencies.
    composer self-update &> /dev/null;
    composer update &> /dev/null;

@endtask

@task('update-permissions')

    {{ msg('Updating directory owner and permissions...') }}

    # cd into releases folder
    cd {{ $releasesDir }};

    # Update group owner and permissions
    chgrp -R www-data {{ $newReleaseName }};
    chmod -R ug+rwx {{ $newReleaseName }};
    chmod -Rf 1777 {{ $newReleaseName }}/storage;

@endtask

@task('update-symlinks')

    {{ msg('Creating symlink to latest release...') }}

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
    chmod -Rf 1777 {{ $baseDir }}/storage;

    ln -nfs {{ $releasesDir }}/{{ $newReleaseName }} {{ $liveDir }};
    chgrp -h www-data {{ $liveDir }};

@endtask

@task('down')

    {{ msg('Putting app in maintenance mode...') }}

    #cd {{ $releasesDir }}/{{ $newReleaseName }};
    cd {{ $liveDir }};
    php artisan down;

@endtask

@task('migrate')

    {{ msg('Running migrations...') }}

    cd {{ $liveDir }};
    php artisan migrate --force;

@endtask

@task('rollback')

    {{ msg('Rolling back last migration...') }}

    cd {{ $liveDir }};
    php artisan migrate:rollback --force;

@endtask

@task('refresh')

    {{ msg('Refreshing database migrations...') }}

    cd {{ $liveDir }};
    php artisan migrate:refresh --force;

@endtask

@task('up')

    cd {{ $liveDir }};
    php artisan up;

@endtask

@task('optimize')

    {{ msg('Optimizing...') }}

    #cd {{ $releasesDir }}/{{ $newReleaseName }};
    cd {{ $liveDir }};

    # Optimize installation.
    php artisan cache:clear;
    php artisan clear-compiled;
    php artisan optimize;
    php artisan config:cache
    php artisan route:cache;

    # Clear the OPCache
    sudo service php5-fpm restart

@endtask

@task('purge-releases')

    {{ msg('Purging old releases...') }}

    # This will list our releases by modification time and delete all but the 5 most recent.
    purging=$(ls -dt {{ $releasesDir }}/* | tail -n +5);

    if [ "$purging" != "" ]; then
        echo Purging old releases: $purging;
        rm -rf $purging;
    else
        echo "No releases found for purging at this time";
    fi

@endtask



{{-- Local tasks --}}

@task('build', ['on' => 'local'])

    cd ~/dev/dinkomo/web/

    # Update bower dependencies.
    # TODO: update bower: https://www.npmjs.com/package/bower-update
    {{ msg('Updating bower dependencies...') }}
    bower update &> /dev/null

    # Update npm dependencies.
    # TODO: update npm: https://www.npmjs.com/package/npm-check-updates
    {{ msg('Updating node dependencies...') }}
    npm update &> /dev/null

    # Build front-end assets.
    {{ msg('Building assets...') }}
    gulp --production &> /dev/null
    gulp --production --back &> /dev/null

@endtask



{{-- Testing Envoy --}}

@task('test-local', ['on' => 'local'])

    {{ msg('Testing Envoy on localhost...') }}

    ls -a

@endtask

@task('test-production', ['on' => 'production'])

    {{ msg('Testing Envoy on production server...') }}

    cd {{ $releasesDir }}
    ls -a

@endtask
