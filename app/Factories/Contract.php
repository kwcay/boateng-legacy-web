<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class Contract
{
    /**t 
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

    /**
     * Creates a new instance of a DataImportFactory.
     *
     * @param string $factory
     */
    public function make($factory)
    {
        $className = 'App\\Factories\\DataImport\\'. $factory;

        return new $className($this->request, $this->response);
    }
}
