<?php

namespace App\Resources\Definition;

use App\Resources\Definition;

class Expression extends Definition
{
    /**
     * @todo   Use localization string
     * @return string
     */
    public function summarize() : string
    {
        return sprintf(
            '%s is an expression in %s',
            $this->getFirstTitle(),
            $this->getLanguageString()
        );
    }

    public function getTitle (): string
    {
        return array_first($this->data->titles)->title;
    }
}
