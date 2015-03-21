<?php
namespace App\Http\Controllers;

/**
 *
 */
class DevController extends Controller
{
	public function landing() {
		return View::make('dev.base');
	}
}
