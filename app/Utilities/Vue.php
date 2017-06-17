<?php

namespace App\Utilities;

class Vue
{
    /**
     * Loads a VueJS template.
     *
     * @param  string $template
     * @return void
     */
    public function load($template)
    {
        include resource_path(
            'assets'.DIRECTORY_SEPARATOR.
            'js'.DIRECTORY_SEPARATOR.
            'components'.DIRECTORY_SEPARATOR.
            $template.'.html'
        );
    }
}
