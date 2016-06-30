<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 *
 */
namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
        // 'Illuminate\Routing\Events\RouteMatched' => [
        //     'App\Listeners\ApiAuthentication',
        // ],
        'App\Events\SaveAlphabetDetails' => [
            'App\Listeners\CheckAlphabetDetails'
        ],
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		//
	}
}
