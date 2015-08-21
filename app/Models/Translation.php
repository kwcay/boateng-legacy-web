<?php namespace App\Models;

use cebe\markdown\MarkdownExtra;
use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use App\Traits\HasParamsTrait as HasParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes, HasParams;

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

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Markdown parser.
        $this->markdown = new MarkdownExtra;
        $this->markdown->html5 = true;
    }

    public function definition() {
        return $this->belongsTo('App\Models\Definition');
    }
}
