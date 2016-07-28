<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2015, all rights reserved.
 */
namespace App\Providers;

use App\Models\Language;
use App\Models\Definition;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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

        // Set application timezone.
        date_default_timezone_set('UTC');
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
    }
}
