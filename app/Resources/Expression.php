<?php

namespace App\Resources;

class Expression extends Definition
{
    /**
     * @return string
     */
    public function summarize()
    {
        return sprintf(
            '&quot;%s&quot; is an expression in %s',
            $this->getFirstTitle(),
            $this->getLanguageString('or')
        );
    }
}
