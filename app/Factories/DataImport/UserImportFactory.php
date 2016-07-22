<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories\DataImport;

use Exception;
use App\Models\User;
use App\Factories\DataImportFactory;

class UserImportFactory extends DataImportFactory
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
            // Performance check.
            if (!array_key_exists('email', $array) || User::findByEmail($array['email'])) {
                $skipped++;
                continue;
            }

            // Create model.
            $model = User::create(array_only($array, [
                'name',
                'email',
                'password',
                'createdAt',
                'deletedAt',
            ]));

            // TODO: params.

            $saved++;
        }

        $this->setMessage($saved .' of '. ($saved + $skipped) .' users updated.');

        return $this;
    }
}
