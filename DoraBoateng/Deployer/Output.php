<?php

namespace DoraBoateng\Deployer;

/**
 * Temporary class to output text to the console.
 */
class Output
{
    /**
     * @param string $text
     * @return string
     */
    public static function black($text)
    {
        return "echo '\033[0;30m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function red($text)
    {
        return "echo '\033[0;31m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function green($text)
    {
        return "echo '\033[0;32m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function brown($text)
    {
        return "echo '\033[0;33m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function blue($text)
    {
        return "echo '\033[0;34m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function purple($text)
    {
        return "echo '\033[0;35m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function cyan($text)
    {
        return "echo '\033[0;36m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lightGray($text)
    {
        return "echo '\033[0;37m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function darkGray($text)
    {
        return "echo '\033[1;30m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lightRed($text)
    {
        return "echo '\033[1;31m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lightGreen($text)
    {
        return "echo '\033[1;32m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function yellow($text)
    {
        return "echo '\033[1;33m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lightBlue($text)
    {
        return "echo '\033[1;34m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lightPurple($text)
    {
        return "echo '\033[1;35m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function lightCyan($text)
    {
        return "echo '\033[1;36m$text\033[0m';\n";
    }

    /**
     * @param string $text
     * @return string
     */
    public static function white($text)
    {
        return "echo '\033[1;37m$text\033[0m';\n";
    }
}
