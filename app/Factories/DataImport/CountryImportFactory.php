<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories\DataImport;

use Exception;
use App\Models\Country;
use App\Factories\DataImportFactory;

class CountryImportFactory extends DataImportFactory
{
    /**
     *
     */
    public function importDataSet()
    {
        // Loop through countries and import them one by one.
        $saved = $skipped = 0;
        foreach ($this->dataArray as $countryArray)
        {
            // Performance check.
            if (!array_key_exists('code', $countryArray) || Country::findByCode($countryArray['code'])) {
                $skipped++;
                continue;
            }

            // Create country model.
            $country = Country::create(array_only($countryArray, [
                'name',
                'altNames',
                'code',
                'createdAt',
                'deletedAt',
            ]));

            $saved++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' countries added to database.');

        return $this;
    }
}
