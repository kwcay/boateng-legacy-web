<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved.
 */
namespace App\Http\Controllers\Admin;

use Exception;
use App\Factories\DataExportFactory as ExportHelper;
use App\Http\Controllers\Controller as BaseController;

class AdminController extends BaseController
{
    /**
     * Admin landing page.
     */
    public function index()
    {
        return view('admin.index');
    }

    /**
     * Import landing page.
     */
    public function import()
    {
        return view('admin.import');
    }

    /**
     * Exports a resource to file.
     *
     * @param string $resourceName
     * @param string $format
     * @return Response
     */
    public function export(ExportHelper $helper, $resourceName, $format)
    {
        // Use the DataExportFactory to export and format data from the database.
        try {
            $result = $helper->exportResource($resourceName, $format);
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
