<?php
/**
 * @file    ExportController.php
 * @brief   Handles export of data, such as definitions, languages, etc.
 */
namespace App\Http\Controllers\Data\v040;

use Session;
use Redirect;

use App\Models\Language;
use App\Models\Definition;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Yaml\Yaml;
use App\Http\Controllers\Controller;


class ExportController extends Controller
{
    /**
     *
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // Define the directory to upload data.
        $this->dataPath = storage_path() .'/app/import';
    }

    public function export($resourceType, $format = 'yaml')
    {
        // Currently, only languages and definitions can be exported.
        if (!in_array($resourceType, ['language', 'definition'])) {
            return redirect(route('admin.export'))->withMessages(['Invalid resource type.']);
        }

        $className = 'App\\Models\\'. ucfirst($resourceType);

        // Double-check data format.
        if (!in_array($format, $className::getExportFormats())) {
            return redirect(route('admin.export'))->withMessages(['Invalid format.']);
        }

        // Format the data to be exported.
        $data = $className::all();
        $export = [
            'meta' => [
                'type' => $resourceType,
                'total' => count($data)
            ],
            'data' => []
        ];

        foreach ($data as $resource) {
            $export['data'][] = array_except($resource->getExportArray(), ['id']);
        }

        $export['meta']['checksum'] = md5(json_encode($export['data']));

        // Disable compression.
        @\ini_set('zlib.output_compression', 'Off');

        // Set some cache-busting headers, set the response content, and send everything to client.
        return $this->response
            ->header('Pragma', 'public')
            ->header('Expires', '-1')
            ->header('Cache-Control', 'public, must-revalidate, post-check=0, pre-check=0')
            ->header('Content-Type', $className::getContentType($format))
            ->header('Content-Disposition',
                $this->response->headers->makeDisposition('attachment', $className::getExportFileName($format)))
            ->setContent($className::export($export, $format))
            ->send();
    }
}
