<?php

namespace App\Resources;

class Definition extends Contract
{
    /**
     * @var string
     */
    protected $locale = 'en-CA';

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var string
     */
    protected $languageString;

    /**
     * @var stdClass
     */
    protected $translation;

    /**
     * @return string
     */
    public function getFirstTitle()
    {
        return array_first($this->data->titles)->title;
    }

    /**
     * @return string
     */
    public function getTitleString()
    {
        return $this->getFirstTitle();
    }

    /**
     * Retrieves language codes
     *
     * @return string[]
     */
    public function getLangCodes()
    {
        return array_pluck($this->data->languages, 'code');
    }

    /**
     * @return string
     */
    public function getLanguageString($concat = 'and')
    {
        return $this->listToString(array_pluck($this->data->languages, 'name'), $concat);
    }

    /**
     * @todo   Localize
     * @return stdClass
     */
    public function getTranslation()
    {
        if (! is_null($this->translation)) {
            return $this->translation;
        }

        foreach ($this->data->translations as $data) {
            if ($data->language == 'eng') {
                $this->translation = $data;
            }
        }

        // TODO: find a better fallback
        if (! $this->translation) {
            if (! $this->translation = array_first($this->data->translations)) {
                $this->translation = new \stdClass;
                $this->translation->practical   = '';
                $this->translation->literal     = '';
                $this->translation->meaning     = '';
            }
        }

        return $this->translation;
    }

    /**
     * @return string
     */
    public function summarize()
    {
        return $this->getFirstTitle().' - '.$this->getLanguageString();
    }

    /**
     * Creates a route to the new definition form.
     *
     * @param  string $type
     * @return string
     */
    public function createRoute($type = 'word')
    {
        return route('definition.create', ['type' => $type, 'lang' => implode(',', $this->getLangCodes())]);
    }
}
