<?php

namespace App\Resources;

class Word extends Definition
{
    /**
     * @return string
     */
    public function summarize()
    {
        return sprintf(
            '%s is &quot;%s&quot; in %s',
            $this->getTranslation()->practical,
            $this->getFirstTitle(),
            $this->getLanguageString()
        );
    }
}
