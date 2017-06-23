<?php

namespace DoraBoateng\Laravel;

use DoraBoateng\Api\Client;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Defers the loading of this provider.
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
            $client = new Client([
                'id'        => config('doraboateng.id'),
                'secret'    => config('doraboateng.secret'),
                'timeout'   => config('doraboateng.timeout', 4.0),
                'api_host'  => config('doraboateng.host', 'https://api.doraboateng.com'),
            ]);

            // Store access token
            $client->addListener(Client::EVENT_SET_ACCESS_TOKEN, function($type, $expires, $token) {
                app('cache')->store()->put('doraboateng.accesstoken', $token, $expires / 60);
            });

            // Retrieve saved access token
            $client->addListener(Client::EVENT_GET_ACCESS_TOKEN, function() {
                return app('cache')->store()->get('doraboateng.accesstoken');
            });

            return $client;
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
