<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
	use ValidatesRequests;

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    /**
     * Gets the embedable relations and attributes that may be appended to a model.
     *
     * @param array|string $embed   The properties to be appended to a model.
     * @param array $appendable     Those properties which aren't database relations.
     * @return array
     */
    protected function getEmbedArray($embed = null, array $appendable = [])
    {
        // Relations and attributes to append
        $embed = is_string($embed) ? @explode(',', $embed) : (array) $embed;

        // Extract the attributes from the list of embeds.
        $attributes = array_intersect($appendable, $embed);

        // Separate the database relations from the appendable attributes.
        foreach ($embed as $key => $embedable)
        {
            // Remove invalid relations.
            $embedable = preg_replace('/[^0-9a-z]/i', '', $embedable);
            if (empty($embedable)) {
                unset($embed[$key]);
            }

            if (in_array($embedable, $attributes)) {
                unset($embed[$key]);
            }
        }

        return [
            'relations' => $embed,
            'attributes' => $attributes
        ];
    }
}
