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
     * Fully parsed data object (including meta data).
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
        $this->dataPath = storage_path() .'/app/data';
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
        }

        return redirect(route('admin.import'))->withMessages([$message]);
    }

    public function export($resourceType, $format = 'yaml')
    {
        //
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

            // Move data file so we can manipulate it. We put the date in the filename so we can
            // keep track on files if ever needed.
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

        // Data files with meta data.
        if (isset($this->dataObject['meta']) && isset($this->dataObject['data']))
        {
            $meta = $this->dataObject['meta'];
            $data = $this->dataObject['data'];

            // Data integrity check.
            if (!isset($meta['checksum']) || $meta['checksum'] != md5(json_encode($data))) {
                $this->error = 'Checksum failed.';
            }

            // Performance check.
            elseif (!is_array($data) || empty($data)) {
                $this->error = 'No data found.';
            }

            // Since our dataset seems valid, try and import it.
            else
            {
                $this->dataSet = $this->importDefinitions($data);
                $this->dataType = $meta['type'];
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
        $data = [];         // This will hold our data to be imported.
        $sortedByCode = []; // This references languages by code.
        $hasParent = [];    // This will hold the index of languages with parents.

        foreach($oldFormat as $oldLang)
        {
            $lang = new Language(array_only($oldLang, ['code', 'countries', 'created_at']));

            // If the language has a code, remember its index in $data so we can try
            // and save the parent language later.
            if (isset($oldLang['parent'])) {
                $hasParent[count($data)] = $oldLang['parent'];
            }

            // Language name.
            if (strpos($oldLang['name'], ',')) {
                $names = @explode(',', $oldLang['name']);
                $lang->name = array_pull($names, 0);
                $lang->alt_names = implode(', ', $names);
            } else {
                $lang->name = $oldLang['name'];
            }

            // Description.
            if (isset($oldLang['desc'])) {
                $lang->setParam('desc', $oldLang['desc']);
            }

            $data[] = $lang;
            $sortedByCode[$lang->code] = $lang;
        }

        // Update parents.
        if (count($hasParent))
        {
            foreach ($hasParent as $index => $parentCode)
            {
                if (isset($sortedByCode[$parentCode]))
                {
                    $data[$index]->parent_code = $parentCode;
                    $data[$index]->setParam('parentName', $sortedByCode[$parentCode]->name);
                }
            }
        }

        return $data;
    }

    public function convertOldDefinitionSet(array $oldFormat)
    {
        $data = [];

        foreach ($oldFormat as $oldDef)
        {
            $def = new \App\Models\Definitions\Word(array_only($oldDef, ['created_at']));
            // $def->setAttribute('type', Definition::TYPE_WORD);

            // Definition data.
            if (strpos($oldDef['word'], ',')) {
                $titles = @explode(',', $oldDef['word']);
                $def->setAttribute('title', array_pull($titles, 0));
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
     * Formats language data into something we can import into our database.
     *
     * @param array $rawData
     * @return array
     */
    public function importLanguages(array $rawData)
    {
        $data = [];

        return $data;
    }

    /**
     * Formats definition data into something we can import into our database.
     *
     * @param array $rawData
     * @return array
     */
    public function importDefinitions(array $rawData)
    {
        $data = [];

        // Create definitions one by one, so that we may update the relations at the same time.
        foreach ($rawData as $raw)
        {
            $def = new \App\Models\Definitions\Word(array_only($raw, ['created_at', 'deleted_at']));

            // Title.
            if (isset($raw['word']))
            {
                if (strpos($raw['word'], ',')) {
                    $titles = @explode(',', $raw['word']);
                    $def->title = array_pull($titles, 0);
                    $def->altTitles = implode(', ', $titles);
                } else {
                    $def->title = $raw['word'];
                }
            }

            elseif (isset($raw['alt_data']))
            {
                $def->title = $raw['data'];
                $def->altTitles = $raw['alt_data'];

                unset($raw['data'], $raw['alt_data']);
            }

            else {
                $def->title = $raw['title'];
                $def->altTitles = $raw['alt_titles'];
            }

            // Data.
            if (isset($raw['data']) && strlen($raw['data'])) {
                $def->setAttribute('data', $raw['data']);
            }

            // Type.
            // $def->type = isset($raw['type']) ? $raw['type'] : Definition::TYPE_WORD;

            // Sub-type (or automatically set a default).
            $def->subType = isset($raw['sub_type']) ? $raw['sub_type'] : $def->subType;

            // Languages.
            $langCodes = [];
            if (isset($raw['language']) && is_array($raw['language'])) {
                $langCodes = array_keys($raw['language']);
            }
            elseif (isset($raw['languages']) && is_string($raw['languages'])) {
                $langCodes = @explode(',', $raw['languages']);
            }
            elseif (isset($raw['language']) && is_string($raw['language'])) {
                $langCodes = @explode(',', $raw['language']);
            }

            $def->setRelationToBeImported('languages', $langCodes);

            // Translation set.
            if (isset($raw['translation']) && is_array($raw['translation']) && !isset($raw['translation']['code']))
            {
                $def->setRelationToBeImported('translations', $raw['translation']['practical']);
                $def->setRelationToBeImported('literals', $raw['translation']['literal']);
                $def->setRelationToBeImported('meanings', $raw['translation']['meaning']);
            }

            else
            {
                // Translations.
                if (isset($raw['translations']) || (isset($raw['translation']) && is_string($raw['translation']))) {
                    $translations = isset($raw['translations']) ? $raw['translations'] : $raw['translation'];
                    $def->setRelationToBeImported('translations', json_decode($translations, true));
                }

                // Literal translations.
                if (isset($raw['literal_translations'])) {
                    $def->setRelationToBeImported('literals', json_decode($raw['literal_translations'], true));
                }

                // Meanings.
                if (isset($raw['meanings']) || isset($raw['meaning']))
                {
                    $meanings = isset($raw['meanings']) ? $raw['meanings'] : $raw['meaning'];
                    $def->setRelationToBeImported('meanings', json_decode($meanings, true));
                }
            }

            // Parameters.
            if (isset($raw['params']))
            {
                if (is_array($raw['params'])) {
                    $def->params = $raw['params'];
                }

                elseif ($params = json_decode($raw['params'], true))
                {
                    // Some old formats will have the sub-type hidden here.
                    if (array_has($params, 'type')) {
                        $def->subType = $params['type'];
                        unset($params['type']);
                    }

                    $def->params = $params;
                }
            }

            // State
            $def->state = isset($raw['state']) ? $raw['state'] : Definition::STATE_VISIBLE;

            $data[] = $def;
        }

        return $data;
    }
}
