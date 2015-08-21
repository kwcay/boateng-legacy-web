<?php namespace App\Http\Controllers;

use Session;
use Redirect;

use App\Models\Language;
use App\Models\Definition;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Yaml\Yaml;
use App\Http\Controllers\Controller;

class DataController extends Controller
{
    /**
     * Directory to temporarily store data files.
     */
    private $dataPath;

    /**
     * Original format of data (e.g. YAML, JSON, etc.)
     */
    private $dataFormat;

    /**
     * Stores the raw data file to be parsed.
     */
    private $rawData;

    /**
     * Model associated with data.
     */
    private $dataType;

    /**
     * Fully parsed data object.
     */
    private $dataObject;

    /**
     * Array containing a set of data.
     */
    private $dataSet;

    /**
     * Error message.
     */
    private $error = '';

    /**
     *
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // Define the directory to upload data.
        $this->dataPath = storage_path() . DIRECTORY_SEPARATOR .'app'. DIRECTORY_SEPARATOR .'data';
    }

    /**
     * Imports data into the database.
     *
     * @throws \Exception
     */
    public function import()
    {
        // Retrieve data.
        if (!$this->getDataFromRequest()) {
            return redirect(route('admin.import'))->withMessages(['Couldn\'t parse data.']);
        }

        // Parse data.
        if (!$this->parseData() || !$this->analyzeData()) {
            return redirect(route('admin.import'))->withMessages([$this->error]);
        }

        // Import data.
        $success = '%d of %d %s were imported into the database.';
        switch ($this->dataType)
        {
            case 'language':
                $results = Language::import($this->dataSet);
                $message = sprintf($success, $results['imported'], $results['total'], 'languages');
                break;

            case 'definition':
                $results = Definition::import($this->dataSet);
                $message = sprintf($success, $results['imported'], $results['total'], 'definitions');
                break;

            default:
                $message = 'Bad data type.';
                return redirect(route('admin.import'))->withMessages(['Bad data type.']);
        }

        return redirect(route('admin.import'))->withMessages([$message]);
    }

    public function export($resourceType, $format = 'yaml')
    {
        // Performance check.
        if (!in_array($resourceType, ['language', 'definition'])) {
            return redirect(route('admin.export'))->withMessages(['Invalid resource type.']);
        }

        // Retrieve data.
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
            $export['data'][] = Arr::except($resource->attributesToArray(), ['id']);
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

    private function getDataFromRequest()
    {
        // Retrieve raw data from file.
        if ($this->request->hasFile('data'))
        {
            $temp = $this->request->file('data');

            // Our data files should have a mime type of 'text/plain'
            if ($temp->getMimeType() != 'text/plain') {
                $this->error = 'Invalid file type.';
                return false;
            }

            // Move data file so we can manipulate it.
            $filename = date('Y-m-d') .'-'. md5($temp->getBasename()) .'.'. $temp->getClientOriginalExtension();
            $file = $temp->move($this->dataPath, $filename);

            // We use the file extension only for parsing purposes.
            $this->dataFormat = $temp->getClientOriginalExtension();

            // Read contents of file.
            $this->rawData = file_get_contents($this->dataPath .'/'. $filename);

            // Delete uploaded file.
            unlink($this->dataPath .'/'. $filename);
        }

        // Retrieve raw data from query.
        elseif ($this->request->has('data') && $this->request->isMethod('post'))
        {
            $this->rawData = $this->request->input('data');

            // Save the data format.
            $this->dataFormat = $this->request->input('format');
        }

        // Quickly sanitize our data.
        $this->rawData = strip_tags(trim($this->rawData));
        $this->dataFormat = preg_replace('/[^a-z]/i', '', $this->dataFormat);

        return true;
    }

    private function parseData()
    {
        // Performance check.
        if (strlen($this->error)) {
            return false;
        } elseif (strlen($this->rawData) < 1) {
            $this->error = 'No data received.';
            return false;
        }

        // Parse data into an array.
        switch ($this->dataFormat)
        {
            case 'yml':
            case 'yaml':
                $this->dataObject = Yaml::parse($this->rawData);
                break;

            case 'json':
                $this->dataObject = json_decode($this->rawData, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    $this->error = json_last_error_msg();
                    return false;
                }
                break;

            default:
                $this->error = 'Invalid data format.';
                return false;
        }

        if (!is_array($this->dataObject)) {
            $this->error = 'Invalid data.';
            return false;
        }

        // Remove duplicates.
        $this->dataObject = array_map('unserialize', array_unique(array_map('serialize', $this->dataObject)));

        return (strlen($this->error) == 0);
    }

    private function analyzeData()
    {
        // Performance check.
        if (strlen($this->error)) {
            return false;
        } elseif (!$this->dataObject || !is_array($this->dataObject) || empty($this->dataObject)) {
            $this->error = 'Couldn\'t import data.';
            return false;
        }

        // Well formatted data files.
        if (isset($this->dataObject['meta']) && isset($this->dataObject['data']))
        {
            $meta = $this->dataObject['meta'];
            $data = $this->dataObject['data'];

            // Data integrity check.
            if (!isset($meta['checksum']) || $meta['checksum'] != md5(json_encode($data))) {
                $this->error = 'Checksum failed.';
            }

            //
            elseif (!is_array($data) || empty($data)) {
                $this->error = 'No data found.';
            }

            else
            {

                $this->error = 'TODO: import and validate well formatted data.';

                $this->dataSet = [];
                $this->dataType = $meta['type'];
                // foreach ($data as $item)
                // {
                //
                // }
            }
        }

        // Pure array.
        elseif (isset($this->dataObject[0]) && isset($this->dataObject[0]['params']))
        {
            // Try to classify this data set.
            $sample = $this->dataObject[0];

            // Old languages file.
            if (isset($sample['code']))
            {
                $this->dataType = 'language';
                $this->dataSet = $this->convertOldLanguageSet($this->dataObject);
            }

            // Old definitions file.
            elseif (isset($sample['word']))
            {
                $this->dataType = 'definition';
                $this->dataSet = $this->convertoldDefinitionSet($this->dataObject);
            }
        }

        return (strlen($this->error) == 0);
    }

    public function convertOldLanguageSet(array $oldFormat)
    {
        $data = [];

        foreach($oldFormat as $oldLang)
        {
            $lang = new Language(Arr::only($oldLang, ['code', 'countries', 'created_at']));

            // Parent language.
            if (isset($oldLang['parent'])) {
                $lang->setAttribute('parent_code', $oldLang['parent']);
            }

            // Language name.
            if (strpos($oldLang['name'], ',')) {
                $names = @explode(',', $oldLang['name']);
                $lang->setAttribute('name', Arr::pull($names, 0));
                $lang->setAttribute('alt_names', implode(', ', $names));
            } else {
                $lang->setAttribute('name', $oldLang['name']);
            }

            // Description.
            if (isset($oldLang['desc'])) {
                $lang->setParam('desc', $oldLang['desc']);
            }

            $data[] = $lang;
        }

        return $data;
    }

    public function convertOldDefinitionSet(array $oldFormat)
    {
        $data = [];

        foreach ($oldFormat as $oldDef)
        {
            $def = new Definition(Arr::only($oldDef, ['created_at']));
            $def->setAttribute('type', Definition::TYPE_WORD);

            // Definition data.
            if (strpos($oldDef['word'], ',')) {
                $titles = @explode(',', $oldDef['word']);
                $def->setAttribute('title', Arr::pull($titles, 0));
                $def->setAttribute('alt_titles', implode(', ', $titles));
            } else {
                $def->setAttribute('title', $oldDef['word']);
            }

            // Other attributes.
            $def->setRelationToBeImported('languages', @explode(',', $oldDef['language']));
            $def->setRelationToBeImported('translations', json_decode($oldDef['translation'], true));
            $def->setRelationToBeImported('meanings', json_decode($oldDef['meaning'], true));

            if ($params = json_decode($oldDef['params'], true)) {
                $def->subType = $params['type'];
            }

            // State
            $def->setAttribute('state', Definition::STATE_VISIBLE);

            $data[] = $def;
        }

        return $data;
    }

    /**
     *
     */
	public function exportLanguages()
    {
        $data = Language::export();
    }

    /**
     *
     */
    public function exportDefinitions()
    {
        $data = Definition::export();
    }

}
