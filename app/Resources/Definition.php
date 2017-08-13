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
                $this->translation = new stdClass;
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
     * @param string $name
     */
    public function __get($name)
    {
        if (property_exists($this->data, $name)) {
            return $this->data->$name;
        }

        throw new \Exception('Property "'.$name.'" does not exist in '.get_called_class());
    }
}
