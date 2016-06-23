<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Factories;


class TransliterationFactory
{
    /**
     * Supported scripts.
     */
    protected $supportedScripts = [
        'Latn',
    ];

    /**
     * Transliterates a string into English characters.
     *
     * @param string $text
     * @param string $script
     * @return string
     */
    public function getTransliteration($text, $script = 'Latn')
    {
        // Performance check.
        if (!is_string($text) || !strlen($text) || !in_array($script, $this->supportedScripts)) {
            return $text;
        }

        return static::make($script)->transliterate($text);
    }

    /**
     * Transliterates a string into English characters. Meant to be overriden by child classes.
     *
     * @param string $text
     * @return string
     */
    public function transliterate($text)
    {
        return $text;
    }

    /**
     * Creates a new TransliterationFactory instance.
     *
     * @param string $script
     * @return App\Factories\TransliterationFactory
     */
    public static function make($script)
    {
        $className = 'App\\Factories\\Transliteration\\'. $script .'TransliterationFactory';

        return new $className;
    }
}
