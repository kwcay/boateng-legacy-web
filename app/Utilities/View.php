<?php
/**
 * Copyright Dora Boateng(TM) 2017, all rights reserved.
 */
namespace App\Utilities;

class View
{
    /**
     * @param  stdClass  $definition
     * @return string
     */
    final public function definitionPageTitle($definition = null) : string
    {
        if (! $definition || empty($definition->mainTitle)) {
            return trans('branding.title');
        }

        $title = $definition->mainTitle;

        // TODO: use all languages, not just "main" langauge (which is deprecated)
        if (isset($definition->mainLanguage) && !empty($definition->mainLanguage->name)) {
            $title .= ' meaning in '. $definition->mainLanguage->name;
        }

        return $title;
    }

    /**
     *
     */
    public function languageCount()
    {
        return number_format(23);
    }

    /**
     *
     */
    public function definitionCount()
    {
        return number_format(1217);
    }
}
