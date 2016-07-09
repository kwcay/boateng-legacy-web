<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories\DataImport;

use Exception;
use App\Models\Culture;
use App\Factories\DataImportFactory;

class CultureImportFactory extends DataImportFactory
{
    /**
     *
     */
    public function importDataSet()
    {
        // Loop through dataset and import each model one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $array)
        {
            // TODO: performance check.
            // ...

            // Create model.
            $model = Culture::create(array_only($array, [
                'name',
                'transliteration',
                'altNames',
                'createdAt',
                'deletedAt',
            ]));

            // TODO: languages.
            // TODO: countries.
            // TODO: media.
            // TODO: data.

            $saved++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' cultures added to database.');

        return $this;
    }
}
