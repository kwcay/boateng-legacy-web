<?php
/**
 * Copyright Dora Boateng(TM) 2015, all rights reserved.
 */
namespace App;

use Request;

class Utilities
{
    /**
     *
     */
    protected static $randomBackground;

    /**
     *
     */
    protected static $backgroundList = [
        [
            'src-large'     => '/img/bg/1ea7f4ef6b4914d253d293e651f22fd9.jpg',
            'src-scaled'    => '/img/bg/1ea7f4ef6b4914d253d293e651f22fd9.jpg',
        ],
        [
            'src-large'     => '/img/bg/2d367c83ace8e17b5d262944c7044aee.jpg',
            'src-scaled'    => '/img/bg/2d367c83ace8e17b5d262944c7044aee.jpg',
        ],
    ];

    /**
     * Retrieves the versioned asset file.
     *
     * @param string $filename
     * @return string
     */
    public static function rev($filename)
    {
        // Find manifest file.
        list($name, $ext) = explode('.', $filename);
        $manifestPath = base_path('resources/assets/build/'.$ext.'/manifest.'.$name.'.json');
        if (! file_exists($manifestPath)) {
            return '';
        }

        // Retrieve manifest.
        if (! $manifest = json_decode(file_get_contents($manifestPath), true)) {
            return '';
        }

        return '/assets/'.$ext.'/'.basename(array_first($manifest));
    }

    /**
     * Returns the relative URI to a random background image.
     *
     * @return string
     */
    public static function bgSrc()
    {
        self::setRandomBackground();

        return self::$randomBackground['src-large'];
    }

    /**
     *
     */
    public static function bgCredits()
    {
        self::setRandomBackground();

        return isset(self::$randomBackground['credits'])
            ? self::$randomBackground['credits']
            : null;
    }

    /**
     *
     */
    public static function bgCreditsUrl()
    {
        self::setRandomBackground();

        return isset(self::$randomBackground['credits-url'])
            ? self::$randomBackground['credits-url']
            : null;
    }

    /**
     *
     */
    public static function isProd()
    {
        return app()->environment() == 'production' || Request::get('prod') == 1;
    }

    /**
     *
     */
    protected static function setRandomBackground()
    {
        if (is_null(self::$randomBackground)) {
            self::$randomBackground = self::$backgroundList[mt_rand(0, sizeof(self::$backgroundList) - 1)];
        }
    }

    /**
     *
     */
    protected static function totalLanguages()
    {
        return 27;
    }
}
