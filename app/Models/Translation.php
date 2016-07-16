<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Models;

use cebe\markdown\MarkdownExtra;
use App\Traits\ValidatableTrait as Validatable;
use App\Traits\ObfuscatableTrait as Obfuscatable;
use App\Traits\ExportableTrait as Exportable;
use App\Traits\HasParamsTrait as HasParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use Validatable, Obfuscatable, Exportable, SoftDeletes, HasParams;


    //
    //
    // Main attributes
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    private $markdown;

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
    * The attributes that should be hidden for arrays.
    */
    protected $hidden = ['id', 'definition_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @var array   Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var array   Validation rules.
     */
    public $validationRules  = [];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['definition'];


    //
    //
    // Relations
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Defines relation to Definition model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function definition() {
        return $this->belongsTo('App\Models\Definition');
    }

    /**
     * Defines relation to Reference model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function references() {
        return $this->morphToMany('App\Models\Reference', 'referenceable', 'referenceable');
    }


    //
    //
    // Main methods
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Markdown parser.
        // $this->markdown = new MarkdownExtra;
        // $this->markdown->html5 = true;
    }
}
