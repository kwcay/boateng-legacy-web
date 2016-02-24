<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class Alphabet extends Model
{
    use CamelCaseAttrs;

    /**
     * Languages using this alphabet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages() {
        return $this->belongsToMany('App\Models\Language');
    }


    //
    //
    // Helper methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Looks up an alphabet model by code.
     *
     * @param string|\App\Models\Alphabet $code
     * @return \App\Models\Alphabet|null
     */
    public static function findByCode($code)
    {
        // Performance check.
        if ($code instanceof static) {
            return $code;
        }

        // Retrieve alphabet by code.
        $code = preg_replace('/[^a-z\-]/', '', strtolower($code));
        return $code ? static::where('code', '=', ucfirst($code))->first() : null;
    }
}
