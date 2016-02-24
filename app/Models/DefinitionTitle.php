<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;

class DefinitionTitle extends Model
{
    use CamelCaseAttrs;


    //
    //
    // Attirbutes used by Illuminate\Database\Eloquent\Model
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * The database table used by the model.
     */
    protected $table = 'definition_titles';

    /**
     * Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden from the model's array form.
     */
    protected $hidden = [
        'id',
        'definition_id',
        'alphabet_id',
    ];

    /**
     * Attributes that SHOULD be appended to the model's array form.
     */
    protected $appends = [];

    /**
     * Attributes that CAN be appended to the model's array form.
     */
    public static $appendable = [];

    /**
     *
     */
    public function alphabet()
    {
        return $this->belongsTo('App\Models\Alphabet');
    }
}
