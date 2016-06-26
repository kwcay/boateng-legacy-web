<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories;

use Exception;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;

class DataImportFactory
{
    /**
     * Supported file formats
     *
     * @var array
     */
    protected $supportedFormats = [
        'bgl',
        'dict', 'dictd',
        'gz', 'tar.gz', 'nko',
        'js', 'json',
        'yml', 'yaml',
        'xml',
    ];

    /**
     * Supported MIME types
     *
     * @var array
     */
    protected $supportedMimeTypes = [
        'text/plain'
    ];

    /**
     * Directory to temporarily store data files.
     *
     * @var string
     */
    private $rawDataPath;

    /**
     *
     *
     * @var File
     */
    protected $rawDataFile;

    /**
     *
     *
     * @var string
     */
    protected $fileName;

    /**
     * Original format of data (e.g. YAML, JSON, etc.)
     *
     * @var string
     */
    protected $dataFormat;

    /**
     * Stores the raw data file to be parsed.
     *
     * @var string
     */
    protected $rawDataString;

    /**
     * Fully parsed data object (including meta data).
     *
     * @var array
     */
    protected $rawDataObject;

    /**
     * Model associated with data.
     *
     * @var string
     */
    protected $dataModel;

    /**
     * Meta data
     *
     * @var array
     */
    protected $dataMeta;

    /**
     * Array containing a set of data.
     *
     * @var array
     */
    protected $dataSet;

    /**
     *
     *
     * @var array
     */
    protected $messages = [];

    /**
     * 
     */
    public function __constructor()
    {
        $this->boot();
    }

    /**
     * Called once class has been instantiated.
     */
    public function boot()
    {
        $this->setDataPath(storage_path() .'/app/import');
    }

    /**
     * Sets the path where data files should be uploaded to.
     *
     * @param string $directory
     */
    public function setDataPath($path)
    {
        $this->rawDataPath = $path;
    }

    /**
     * @param File $rawDataFile
     * @return DataImportFactory
     */
    public function importFromFile(File $rawDataFile)
    {
        $this->setDataFile($rawDataFile);

        // Move data file so we can manipulate it.
        $movedDataFile = $this->rawDataFile->move($this->rawDataPath, $this->getFileName());

        // Open/parse data file.
        switch ($this->dataFormat)
        {
            // Single data file.
            case 'js':
            case 'json':
            case 'yml':
            case 'yaml':

                // Read contents of file.
                $this->setRawData(file_get_contents($this->rawDataPath .'/'. $this->getFileName()));

                // Parse raw data into an array.
                $this->parseRawData();

                // Import a simple data set
                $results = $this->importDataSet();
                break;

            // Combined data file.
            case 'gz':
            case 'tar.gz':
            case 'nko':
                throw new Exception('Combined data file not yet supported :/');
                break;

            // Other unsupported formats.
            case 'bgl':
            case 'dict':
            case 'dictd':
            case 'xml':
            default:
                throw new Exception('"'. $this->dataFormat .'" format not yet supported :/');
                break;
        }

        // Delete uploaded file.
        unlink($this->rawDataPath .'/'. $this->getFileName());

        return $results;
    }

    /**
     * @param File $rawDataFile
     */
    public function setDataFile(File $rawDataFile)
    {
        $this->rawDataFile = $rawDataFile;

        // Performance check.
        if (!in_array($this->rawDataFile->getMimeType(), $this->supportedMimeTypes))
        {
            throw new Exception('Unsupported MIME type :/');
        }

        // We use the file extension only for parsing purposes.
        $this->setDataFormat($this->rawDataFile->getClientOriginalExtension());

        // Reset other properties.
        $this->fileName = null;
        $this->rawDataObject = null;
        $this->dataMeta = null;
        $this->dataArray = null;
        $this->dataModel = null;
    }

    /**
     * @param string $format
     */
    public function setDataFormat($format)
    {
        $this->dataFormat = strtolower(preg_replace('/[^a-z\.]/i', '', $format));

        // Performance check
        if (!in_array($this->dataFormat, $this->supportedFormats))
        {
            throw new Exception('Unsupported file format :/');
        }
    }

    /**
     * Creates a unique filename for this data set.
     *
     * @return string|null
     */
    public function getFileName()
    {
        if (is_null($this->fileName) && $this->hasValidDataFile())
        {
            // TODO: make file name format a configurable parameter.
            $this->fileName =
                date('Y-m-d') .'-'.
                md5($this->rawDataFile->getBasename()) .'.'.
                $this->rawDataFile->getClientOriginalExtension();
        }

        return $this->fileName;
    }

    /**
     * @param string $raw
     */
    public function setRawData($raw)
    {
        $this->rawDataString = trim($raw);
    }

    /**
     * Parses a single, native data file.
     */
    public function parseRawData()
    {
        // Performance check.
        if (strlen($this->rawDataString) < 1) {
            throw new Exception('No data received.');
        }

        switch ($this->dataFormat)
        {
            case 'yml':
            case 'yaml':
                $this->rawDataObject = Yaml::parse($this->rawDataString);
                break;

            case 'js':
            case 'json':
                $this->rawDataObject = json_decode($this->rawDataString, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg());
                }
                break;
        }

        // Check that data really was parsed.
        if (!is_array($this->rawDataObject) || empty($this->rawDataObject)) {
            throw new Exception('Invalid data.');
        }

        // Check format of array.
        if (!isset($this->rawDataObject['meta']) || !isset($this->rawDataObject['data'])) {
            throw new Exception('Invalid data structure.');
        }

        $this->setDataMeta($this->rawDataObject['meta']);
        $this->setDataArray($this->rawDataObject['data']);
        if (!is_array($this->dataArray) || empty($this->dataArray)) {
            throw new Exception('No data found.');
        }

        // Data integrity check.
        if (!$this->isDataIntegral()) {
            throw new Exception('Checksum failed.');
        }

        // Set data model.
        $this->setDataModel();

        // Remove duplicates.
        $this->dataArray = array_map('unserialize',
                                array_unique(array_map('serialize', $this->dataArray)));
    }

    /**
     * @param array $meta
     */
    public function setDataMeta(array $meta)
    {
        $this->dataMeta = $meta;
    }

    /**
     * @param array $data
     */
    public function setDataArray(array $data)
    {
        $this->dataArray = $data;
    }

    /**
     * Checksum.
     */
    public function isDataIntegral()
    {
        // Performance check.
        if (!$this->hasValidDataFile() || !isset($this->dataMeta['checksum'])) {
            return false;
        }

        // Build checksum.
        switch (@$this->dataMeta['schema'])
        {
            case 'dinkomo-1':
            default:
                $checksum = md5(json_encode($this->dataArray));
        }

        return $this->dataMeta['checksum'] === $checksum;
    }

    /**
     * Returns true if $this->rawDataFile is a valid file.
     *
     * @return bool
     */
    public function hasValidDataFile() {
        return $this->rawDataFile instanceof File;
    }

    /**
     * Determines the data type and associates it with the related model.
     *
     * @param mixed $model
     */
    public function setDataModel($model = null)
    {
        // Set model from meta data.
        if (!$model)
        {
            switch (strtolower($this->dataMeta['type']))
            {
                case 'language':
                    $this->dataModel = 'App\\Models\\Language';
                    break;

                case 'definition':
                    $this->dataModel = 'App\\Models\\Definition';
                    break;

                case 'definition/word':
                case 'definition/expression':
                case 'definition/story':
                    $this->dataModel = 'App\\Models\\'. str_replace('/', '\\\\', $this->dataMeta['type']);
            }
        }

        // Set model from object.
        // TODO
        elseif (false)
        {

        }

        // Set model directly.
        elseif (is_string($model)) {
            $this->dataModel = $model;
        }
    }

    /**
     * Imports a data set into the database.
     *
     * @return DataImportFactory
     */
    public function importDataSet()
    {
        // Performance check.
        if (count($this->dataArray) < 1) {
            throw new Exception('Empty data set.');
        }

        // Since we're in the general DataImportFactory, we will create a new factory that
        // is specific to this data set.
        switch ($this->dataModel)
        {
            case 'App\\Models\\Language':
                $factory = $this->make('LanguageImportFactory');
                break;

            case 'App\\Models\\Definition':
            case 'App\\Models\\Definition\\Word':
            case 'App\\Models\\Definition\\Expression':
            case 'App\\Models\\Definition\\Story':
                $factory = $this->make('DefinitionImportFactory');
                break;

            default:
                throw new Exception('Invalid data model.');
        }

        $factory->setDataModel($this->dataModel);
        $factory->setDataMeta($this->dataMeta);
        $factory->setDataArray($this->dataArray);

        // TODO: check for infinite loops?

        return $factory->importDataSet();
    }

    /**
     * @param string $msg
     */
    public function setMessage($msg)
    {
        array_push($this->messages, $msg);
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Creates a new instance of a DataImportFactory.
     *
     * @param string $factory
     */
    public function make($factory)
    {
        $className = 'App\\Factories\\DataImport\\'. $factory;

        return new $className;
    }
}
