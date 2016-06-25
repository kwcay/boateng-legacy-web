<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories\Transliteration;

use Exception;
use App\Factories\TransliterationFactory;

class LatnTransliterationFactory extends TransliterationFactory
{
    /**
     * Transliterates a string into English characters. Meant to be overriden by child classes.
     *
     * @param string $text
     * @return string
     */
    public function transliterate($text)
    {
        return str_replace(
            ['ɛ', 'Ɛ', 'ŋ', 'Ŋ', 'ɔ', 'Ɔ'],
            ['e', 'E', 'n', 'N', 'o', 'O'],
            $text
        );
    }
}
