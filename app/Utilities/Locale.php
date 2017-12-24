<?php

namespace App\Utilities;

class Locale
{
    /**
     * @return string
     */
    public static function set()
    {
        /** @var \Mcamara\LaravelLocalization\LaravelLocalization $localization */
        $localization = app()->make('laravellocalization');
        $locale       = $localization->setLocale();

        return $locale ?: (app()->environment() === 'testing' ? $localization->getDefaultLocale() : $locale);
    }
}
