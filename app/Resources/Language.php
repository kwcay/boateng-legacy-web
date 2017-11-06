<?php

namespace App\Resources;

/**
 * @property string $code
 * @property string $parentCode
 * @property string $name
 */
class Language extends Contract
{
    public function getFirstName()
    {
        if (! trim($this->name) || strpos($this->name, ',') === false) {
            return $this->name;
        }

        return trim(substr($this->name, 0, strpos($this->name, ',')));
    }
}
