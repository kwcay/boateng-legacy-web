<?php

namespace App\Resources;

/**
 * @property string $code
 * @property string $parentCode
 * @property string $name
 * @property string $altNames
 * @property array  $children
 */
class Language extends Resource
{
    public function __construct (\stdClass $data)
    {
        parent::__construct($data);

        // Convert children languages
        $this->data->children = empty($this->data->children) ? [] : array_map(function ($lang) {
            return new Language($lang);
        }, $this->data->children);
    }

    /**
     * @return string
     */
    public function summarize()
    {
        return trans('language.summary', ['language' => $this->getFullName()]);
    }

    /**
     * @todo   Temporary, the API shouldn't return language names with commas in them.
     * @return string
     */
    public function getFirstName()
    {
        if (! trim($this->name) || strpos($this->name, ',') === false) {
            return $this->name;
        }

        return trim(substr($this->name, 0, strpos($this->name, ',')));
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name.($this->altNames ? ' ('.trim($this->altNames).')' : '');
    }

    /**
     * @return string
     */
    public function listChildren()
    {
        if (empty($this->data->children)) {
            return '';
        }

        return $this->listToString(array_map(function($lang) {
            /** @var Language $lang */
            return '<a href="'.route('language', $lang->code).'">'.$lang->getFirstName().'</a>';
        }, $this->data->children), 'and');
    }
}
