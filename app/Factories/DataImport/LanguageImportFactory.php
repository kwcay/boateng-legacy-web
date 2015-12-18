<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Factories\DataImport;

use Exception;
use App\Models\Language;
use App\Factories\DataImportFactory;

class LanguageImportFactory extends DataImportFactory
{
    /**
     *
     */
    public function importDataSet()
    {
        // TODO: check database for duplicates
        // ...

        // Loop through languages and import them one by one.
        $saved = $skipped = 0;
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

            // TODO: relations to be added:
            // Has Many: Culture
            // Has Many: Data
            // Has Many: Country
            // Has Many: Script

            $lang->save() ? $saved++ : $skipped++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' languages added to database.');

        return $this;
    }
}
