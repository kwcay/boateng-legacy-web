<?php
/**
 *
 */
namespace App\Factories\DataImport;

use Exception;
use App\Factories\DataImportFactory;

class LanguageImportFactory extends DataImportFactory
{
    /**
     *
     */
    public function importDataSet()
    {
        // Performance check.
        if (count($this->dataArray) < 1) {
            throw new Exception('Empty data set.');
        }

        $data = [];         // This will hold our data to be imported.
        $sortedByCode = []; // This references languages by code.
        $hasParent = [];    // This will hold the index of languages with parents.

        // Loop through languages and import them one by one.
        foreach ($this->dataArray as $langArray)
        {
            // Create a language object.
            $lang = new Language(array_only($langArray, [
                'code',
                'parent_code',
                'name',
                'alt_names',
                'created_at',
                'deleted_at'
            ]));
        }

        $this->setMessage('Dev mode.');

        return $this;
    }
}
