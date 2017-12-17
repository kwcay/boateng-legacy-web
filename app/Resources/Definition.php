<?php

namespace App\Resources;

/**
 * @property int $uniqueId
 */
class Definition extends Resource
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
     * @var \stdClass
     */
    protected $translation;

    /**
     * Wraps a definition object in its related Definition class.
     *
     * @param  \stdClass $data
     * @return static
     */
    public static function from($data)
    {
        if (! $data || empty($data->type)) {
            return new static($data);
        }

        switch ($data->type) {
            case 'word':
                return new Definition\Word($data);

            case 'expression':
                return new Definition\Expression($data);

            default:
                return new static($data);
        }
    }

    public function summarize() : string
    {
        return $this->getFirstTitle().' ('.$this->getLanguageString().')';
    }

    public function getTitle (): string
    {
        return $this->getTitleString();
    }

    public function route (): string
    {
        return route('definition.show', $this->data->uniqueId);
    }

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
     * @return \stdClass
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
     * Creates a route to the new definition form.
     *
     * @param  string $type
     * @return string
     */
    public function createRoute($type = 'word')
    {
        return route('definition.create', [
            'type' => $type,
            'lang' => implode(',', $this->getLangCodes())
        ]);
    }
}
