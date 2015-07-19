<?php namespace App\Traits;

use Validator;

trait ValidatableResourceTrait
{
    /**
     * @param $data
     * @return mixed
     */
    public static function validate($data) {
        return Validator::make($data, (new static)->validationRules);
    }
}

