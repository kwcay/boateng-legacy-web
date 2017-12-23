<?php

namespace App\Resources;

/**
 * @property string $resourceType
 * @property string $createdAt
 * @property string $updatedAt
 */
abstract class Resource
{
    /**
     * @var \stdClass
     */
    protected $data;

    /**
     * @todo  Make protected
     * @param \stdClass $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param  $data
     * @return \App\Resources\Definition|\App\Resources\Language|null
     */
    public static function from($data)
    {
        if (! $data || empty($data->resourceType)) {
            return null;
        }

        switch ($data->resourceType) {
            case 'definition':
                return Definition::from($data);

            case 'language':
                return new Language($data);

            default:
                return null;
        }
    }

    abstract public function summarize() : string;

    /**
     * Standardized getter for the title of this resource.
     *
     * @return string
     */
    abstract public function getTitle() : string;

    /**
     * Generates a URI for the resource.
     *
     * @return string
     */
    abstract public function route() : string;

    /**
     * @todo   Localize
     * @param  array  $list
     * @return string
     */
    public function listToString(array $list, $concat)
    {
        switch (count($list)) {
            case 0:
                $string = '?';
                break;

            case 1:
                $string = array_first($list);
                break;

            default:
                $string = implode(', ', array_slice($list, 0, count($list) - 1));
                $string .= ' '.$concat.' '.array_last($list);
        }

        return $string;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return property_exists($this->data, $name)
            ? $this->data->$name
            : null;
    }
}
