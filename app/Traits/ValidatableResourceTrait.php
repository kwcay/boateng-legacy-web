<?php namespace App\Traits;

use Validator;

trait ValidatableResourceTrait
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

