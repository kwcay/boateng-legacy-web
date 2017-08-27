<?php

namespace App\Resources;

class Expression extends Definition
{
    /**
     * @todo   Use localization string
     * @return string
     */
    public function summarize()
    {
        return sprintf(
            '%s is an expression in %s',
            $this->getFirstTitle(),
            $this->getLanguageString()
        );
    }
}
