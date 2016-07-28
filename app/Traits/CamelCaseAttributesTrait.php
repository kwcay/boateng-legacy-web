<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2015, all rights reserved.
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
    public function getAttribute($key)
    {
        return parent::getAttribute(snake_case($key));
    }

    /**
     * General mutator which saves attributes under their snake_case names.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        return parent::setAttribute(snake_case($key), $value);
    }

    /**
     * Converts the model instance to an array, and changes the snake_case keys to camelCase.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->convertToCamelCase(parent::toArray());
    }

    /**
     * Convert the model's attributes to an array, and changes the snake_case keys to camelCase.
     *
     * @return array
     */
    public function attributesToArray()
    {
        return $this->convertToCamelCase(parent::attributesToArray());
    }

    /**
     * Converts an array's keys to camelCase.
     *
     * @param array $snakeCaseAttributes
     * @return array
     */
    protected function convertToCamelCase(array $snakeCaseAttributes)
    {
        $camelCasedAttributes = [];

        foreach ($snakeCaseAttributes as $key => $value) {
            $camelCasedAttributes[camel_case($key)] = $value;
        }

        return $camelCasedAttributes;
    }
}
