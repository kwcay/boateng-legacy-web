<?php namespace App\Providers;

use App\Models\Language;
use App\Models\Definition;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Register event listeners on Language model.
		Language::saved(['\\App\\Models\\Language', 'checkAttributes']);

		// Register event listeners for Definition model.
		Definition::saving(['\\App\\Models\\Definition', 'checkAttributes']);
		Definition::saved(['\\App\\Models\\Definition', 'importRelations']);
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);
	}

}
