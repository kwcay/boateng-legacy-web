<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Traits;

trait CamelCaseAttributesTrait
{
    /**
     * General accessor which retrieves attributes by their camelCase names.
     *
     * @param string $key
     * @param return mixed
     */
    public function getAttribute($key) {
        return parent::getAttribute(snake_case($key));
    }

    /**
     * General mutator which saves attributes under their snake_case names.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value) {
        return parent::setAttribute(snake_case($key), $value);
    }

    /**
     * Converts the model instance to an array, and changes the snake_case keys to camelCase.
     *
     * @return array
     */
    public function toArray()
    {
        $camelCasedAttributes = [];
        $snakeCaseAttributes = parent::toArray();

        foreach ($snakeCaseAttributes as $key => $value) {
            $camelCasedAttributes[camel_case($key)] = $value;
        }

        return $camelCasedAttributes;
    }
}
