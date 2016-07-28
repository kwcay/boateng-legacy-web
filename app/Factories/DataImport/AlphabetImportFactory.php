<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
 */
namespace App\Factories\DataImport;

use App\Models\Alphabet;
use App\Factories\DataImportFactory;

class AlphabetImportFactory extends DataImportFactory
{
    public function importDataSet()
    {
        // Loop through dataset and import each model one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $array) {
            // Performance check.
            if (! array_key_exists('code', $array) || Alphabet::findByCode($array['code'])) {
                $skipped++;
                continue;
            }

            // Create country model.
            $model = Alphabet::create(array_only($array, [
                'name',
                'transliteration',
                'code',
                'scriptCode',
                'letters',
                'createdAt',
                'deletedAt',
            ]));

            $saved++;
        }

        $this->setMessage($saved.' of '.($saved + $skipped).' alphabets updated.');

        return $this;
    }
}
