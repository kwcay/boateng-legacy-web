<?php

namespace App\Utilities;

class DefinitionHelper
{
    /**
     *  Temporary helper method to get localized translation.
     */
    public static function trans($definition, $type = 'practical')
    {
        $translation = '';

        foreach ($definition->translations as $data) {
            if ($data->language == 'eng') {
                $translation = $data->$type;
            }
        }

        return $translation;
    }
}
