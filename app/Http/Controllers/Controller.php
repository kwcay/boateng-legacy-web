<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Response;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
	use DispatchesCommands, ValidatesRequests;

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
     * @param array|string $embed
     * @param array $appendable
     * @return array
     */
    protected function getEmbedArrays($embed = null, array $appendable = [])
    {
        // Relations and attributes to append
        $embed = is_string($embed) ? @explode(',', $embed) : (array) $embed;

        // Extract the attributes from the list of embeds.
        $attributes = array_intersect($appendable, $embed);

        // Separate the database relations from the appendable attributes.
        foreach ($embed as $key => $relation)
        {
            // Remove invalid relations.
            $relation = preg_replace('/[^0-9a-z]/i', '', $relation);
            if (empty($relation)) {
                unset($embed[$key]);
            }

            if (in_array($relation, $attributes)) {
                unset($embed[$key]);
            }
        }

        return [
            'relations' => $embed,
            'attributes' => $attributes
        ];
    }

    /**
     * @param mixed $data       Data to be sent.
     * @param array $headers    Headers to be sent with response.
     * @return string           JSON-encoded string.
     *
     * @deprecated
     */
    public function send($data, $headers = []) {
        return Response::json(['status' => 200, 'results' => $data], 200, $headers);
    }

    /**
     * Shortcut for sending '401 Unauthorized' JSON response.
     *
     * @deprecated
     */
    public function abort($code = 401, $msg = 'Unauthorized') {
        return Response::json(['status' => $code, 'message' => $msg], $code);
    }

    /**
     * Shortcut to send a response to the client with a specific HTTP code.
     *
     * @param int $status   The HTTP status to send.
     * @param mixed $data   The data to send to the client.
     * @return Response
     *
     * @deprecated
     */
    public function error($status, $msg = '', array $headers = [])
    {
        return Response::make($msg, $status, $headers);
    }
}
