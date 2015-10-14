<?php
/**
 *
 */
namespace App\Factories;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract Contract
{
    /**
     *
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->boot();
    }

    /**
     * Called once class has been instantiated.
     */
    public function boot() {}
}
