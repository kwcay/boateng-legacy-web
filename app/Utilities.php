<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Utilities extends Model
{
    /**
     * Retrieves the revisioned asset file.
     *
     * @param string $filename
     * @return string
     */
    public static function rev($filename)
    {
        // Find manifest file.
        list($name, $ext) = explode('.', $filename);
        $manifestPath = base_path('resources/assets/build/'. $ext .'/rev-manifest.json');
        if (!file_exists($manifestPath)) {
            return '';
        }

        // Retrieve manifest.
        if (!$manifest = json_decode(file_get_contents($manifestPath), true)) {
            return '';
        }

        // Check that asset has a mapping.
        if (!array_key_exists($filename, $manifest)) {
            return '';
        }

        return '/assets/'. $ext .'/'. $manifest[$filename];
    }
}
