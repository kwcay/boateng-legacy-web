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
	 * Register application services.
	 *
	 * @return void
	 */
	public function register()
	{
        // Register "Registrar" container binding.
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);

        // Register "ImportController" containter binding.
        $this->app->bind(
			'App\Http\Controllers\Data\v040\ImportController',
			'App\Http\Controllers\ImportController'
		);

        // Register "ExportController" container binding.
        $this->app->bind(
			'App\Http\Controllers\Data\v040\ExportController',
			'App\Http\Controllers\ExportController'
		);
	}
}
