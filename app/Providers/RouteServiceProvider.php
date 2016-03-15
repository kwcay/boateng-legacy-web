<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		// Global patterns
        $router->pattern('id', '[0-9A-Za-z]+');
        $router->pattern('code', '[a-z]{3}|[a-z]{3}-[a-z]{3}');

        parent::boot($router);
	}

	/**
	 * Defines the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
        // Routes for API v0.1
		$router->group([
            'prefix' => 'api/0.1',
            'namespace' => 'App\Http\Controllers\API\v01',
            'middleware' => ['api.headers'/*, 'api.auth'*/]],
            function($router) {
    			require app_path('Http/Routes/API/0.1.php');
		    }
        );

        // Admin routes.
		$router->group([
            'prefix' => 'admin',
            'namespace' => $this->namespace,
            'middleware' => ['auth']],
            function($router) {
    			require app_path('Http/Routes/admin.php');
		    }
        );

        // General routes.
		$router->group([
            'namespace' => $this->namespace],
            function($router) {
			require app_path('Http/Routes/general.php');
		});
	}

}
