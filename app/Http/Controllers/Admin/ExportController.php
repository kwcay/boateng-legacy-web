<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Factories\DataExportFactory as ExportHelper;

class ExportController extends Controller
{
    /**
     * Injects the dependencies into the controller.
     *
     * @param ExportHelper $helper
     * @param Request $request
     * @param Response $response
     */
    public function __construct(ExportHelper $helper, Request $request, Response $response)
    {
        $this->exportHelper = $helper;
        $this->request = $request;
        $this->response = $response;
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
        try
        {
            $result = $this->exportHelper->exportResource($resourceName, $format);
        }
        catch (Exception $e)
        {
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
