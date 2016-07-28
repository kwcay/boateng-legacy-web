<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
 */
namespace App\Factories\DataImport;

use App\Models\Reference;
use App\Factories\DataImportFactory;

class ReferenceImportFactory extends DataImportFactory
{
    public function importDataSet()
    {
        // Loop through dataset and import each model one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $array) {
            // TODO: performance check.
            // ...

            // Create model.
            $model = Reference::create(array_only($array, [
                'type',
                'data',
                'string',
            ]));

            $saved++;
        }

        $this->setMessage($saved.' of '.($saved + $skipped).' references added to database.');

        return $this;
    }
}
