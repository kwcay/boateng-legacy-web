<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Session;
use Redirect;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Factories\DataImportFactory as ImportHelper;

class ImportController extends Controller
{
    /**
     * Injects the dependencies into the controller.
     *
     * @param ImportHelper $helper
     * @param Request $request
     * @param Response $response
     */
    public function __construct(ImportHelper $helper, Request $request, Response $response)
    {
        $this->importHelper = $helper;
        $this->request = $request;
        $this->response = $response;
    }
    
    /**
     * Imports data into the database.
     */
    public function import()
    {
        // Use the DataImportFactory to parse and import data into the database.
        try
        {
            $result = $this->importHelper->importFromFile($this->request->file('data'));
        }
        catch (Exception $e)
        {
            return redirect(route('admin.import'))->withMessages([$e->getMessage()]);
        }

        return redirect(route('admin.import'))->withMessages($result->getMessages());
    }
}
