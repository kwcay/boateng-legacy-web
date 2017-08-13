<?php

namespace App\Resources;

abstract class Contract
{
    /**
     * @var stdClass
     */
    protected $data;

    /**
     * @param stdClass $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

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
                $names = array_pluck($this->data->languages, 'name');
                $string = implode(', ', array_slice($list, 0, count($list) - 1));
                $string .= ' '.$concat.' '.array_last($list);
        }

        return $string;
    }
}
