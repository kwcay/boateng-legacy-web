<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ObfuscatorServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('Obfuscator', function($app) {
            return new \Hashids\Hashids('rlr6x9a/1=^B8vQg8!0n5w]-K`&$usKE', 8);
        });
	}

    /**
     * Returns the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Obfuscator'];
    }

}
