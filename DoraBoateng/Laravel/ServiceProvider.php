<?php

namespace DoraBoateng\Laravel;

use DoraBoateng\Api\Client;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * @return void
     */
    public function boot()
    {
        // Register event listeners
        Event::listen('Events\Logout', 'DoraBoateng\Laravel\Listeners\Logout');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client([
                'id'        => config('doraboateng.id'),
                'secret'    => config('doraboateng.secret'),
                'timeout'   => config('doraboateng.timeout', 4.0),
                'api_host'  => config('doraboateng.host', 'https://api.doraboateng.com'),

                'temp_cache'    => app('cache')->store()
            ]);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }
}
