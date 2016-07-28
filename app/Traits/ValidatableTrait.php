<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 */
namespace App\Traits;

use Validator;

trait ValidatableTrait
{
    /**
     * @param $data
     * @return mixed
     */
    public static function validate($data)
    {
        // Make sure we have an array of data.
        $data = (array) $data;

        return Validator::make($data, (new static)->validationRules);
    }
}
