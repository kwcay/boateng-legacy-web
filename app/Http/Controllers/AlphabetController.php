<?php
/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
 */
namespace App\Http\Controllers;

use App\Models\Alphabet;
use App\Factories\TransliterationFactory as Transliterator;

/**
 * @abstract Main controller for the Alphabet resource.
 */
class AlphabetController extends Controller
{
    protected $defaultQueryLimit = 20;


    protected $supportedOrderColumns = [
        'id' => 'ID',
        'name' => 'Name',
        'code' => 'Code',
        'scriptCode' => 'Script code',
        'createdAt' => 'Created date',
    ];


    protected $defaultOrderColumn = 'name';

    /**
     * {@inheritdoc}
     */
    protected function getAttributesFromRequest()
    {
        $attributes = parent::getAttributesFromRequest();

        // Update transliteration.
        // TODO: retrieve proper script.
        $script = 'Latn';
        $attributes['transliteration'] = Transliterator::make($script)->transliterate($attributes['name']);

        return $attributes;
    }


    protected $defaultOrderDirection = 'asc';
}
