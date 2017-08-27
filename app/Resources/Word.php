<?php

namespace App\Resources;

class Word extends Definition
{
    /**
     * @return string
     */
    public function getTitleString()
    {
        switch (count($this->data->titles)) {
            case 0:
            case 1:
                $title = $this->getFirstTitle();
                break;

            default:
                $altTitles = array_pluck(array_slice($this->data->titles, 1), 'title');
                $title = $this->getFirstTitle().' ('.implode(', ', $altTitles).')';
        }

        return $title;
    }

    /**
     * @todo   Use localization string
     * @return string
     */
    public function summarize()
    {
        return sprintf(
            '%s means %s in %s',
            $this->getFirstTitle(),
            $this->getTranslation()->practical,
            $this->getLanguageString()
        );
    }
}
