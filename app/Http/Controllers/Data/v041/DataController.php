<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2015, all rights reserved.
 */
namespace App\Http\Controllers\Data\v041;

use Redirect;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Factories\DataExportFactory as ExportHelper;
use App\Factories\DataImportFactory as ImportHelper;

class DataController extends Controller
{
    /**
     * Injects the dependencies into the controller.
     *
     * @param ImportHelper $helper
     * @param Request $request
     * @param Response $response
     */
    public function __construct(ImportHelper $import, ExportHelper $export, Request $request, Response $response)
    {
        $this->importHelper = $import;
        $this->exportHelper = $export;
        $this->request = $request;
        $this->response = $response;

        // Define the directory to upload data.
        $this->dataPath = storage_path().'/app/import';
    }

    /**
     * Imports data into the database.
     */
    public function import()
    {
        // Use the DataImportFactory to parse and import data into the database.
        try {
            $result = $this->importHelper->importFromFile($this->request->file('data'));
        } catch (Exception $e) {
            return redirect(route('admin.import'))->withMessages([$e->getMessage()]);
        }

        return redirect(route('admin.import'))->withMessages($result->getMessages());
    }

    /**
     * Exports a resource to file.
     *
     * @param string $resourceName
     * @param string $format
     */
    public function export($resourceName, $format)
    {
        // Use the DataExportFactory to export and format data from the database.
        try {
            $result = $this->exportHelper->exportResource($resourceName, $format);
        } catch (Exception $e) {
            return redirect(route('admin.export'))->withMessages([$e->getMessage()]);
        }

        // Disable compression.
        @\ini_set('zlib.output_compression', 'Off');

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', $result['Content-Type'])
            ->header('Content-Disposition',
                $this->response->headers->makeDisposition('attachment', $result['filename']))
            ->setContent($result['content']);
    }
}
