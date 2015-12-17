<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Traits;

trait CamelCaseAttributesTrait
{
    /**
     * @param string $key
     * @param return mixed
     */
    public function getAttribute($key) {
        return parent::getAttribute(snake_case($key));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value) {
        return parent::setAttribute(snake_case($key), $value);
    }
}
