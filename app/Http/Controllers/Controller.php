<?php namespace App\Http\Controllers;

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
     */
    public function error($status, $msg = '', array $headers = [])
    {
        return Response::make($msg, $status, $headers);
    }
}
