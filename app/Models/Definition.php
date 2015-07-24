<?php namespace App\Models;

use URL;

use App\Traits\ValidatableResourceTrait as Validatable;
use App\Traits\ObfuscatableResourceTrait as Obfuscatable;
use App\Traits\ExportableResourceTrait as Exportable;
use App\Traits\ImportableResourceTrait as Importable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Definition extends Model
{
    use Validatable, Obfuscatable, Exportable, Importable, SoftDeletes;

    /**
     * @var int     Internal identifier.
     */
    public $id = 0;

    /**
     * @var int     Data type.
     */
    public $type = 0;

    /**
     * @var string  Data.
     */
    public $data = '';

    /**
     * @var string  Alternate forms of the data.
     */
    public $alt_data = '';

    /**
     * @var string  Comma-separated languages.
     */
    public $languages = '';

    /**
     * @var string  JSON-encoded translations.
     */
    public $translations = '';

    /**
     * @var string  JSON-encoded meanings.
     */
    public $meanings = '';

    /**
     * @var string  Source of data.
     */
    public $source = '';

    /**
     * @var string  Tags.
     */
    public $tags = '';

    /**
     * @var int     State of data.
     */
    public $state = -1;

    /**
     * @var string  JSON-encoded parameters.
     */
    public $params = '';

    /**
     * @var array   Attributes which aren't mass-assignable.
     */
    protected $guarded = ['id'];

    /**
     * @var array   Attributes that should be mutated to dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array  Array to help validate input data
     */
    public $validationRules = [
        'word'      => 'required|min:2',
        'language'  => 'required|min:2|regex:/^([a-z, ]+)$/',
//        'type'      => 'in:adj,adv,conn,ex,pre,pro,n,v'
    ];

    /**
     * See: http://www.edb.utexas.edu/minliu/pbl/ESOL/help/libry/speech.htm
     * See: http://www.aims.edu/student/online-writing-lab/grammar/parts-of-speech
     *
     * @var array   Parts of speech.
     */
    public $partsOfSpeech = [
        'adj'   => 'adjective',
        'adv'   => 'adverb',
        'conn'  => 'connective',
        'ex'    => 'exclamation',
        'pre'   => 'preposition',
        'pro'   => 'pronoun',
        'n'     => 'noun',
        'v'     => 'verb',
    ];

    public function getUri($full = true) {
        return route('definition.show', ['id' => $this->getId()], $full);
    }

    public function getWordUri($full = true) {
        $path   = $this->getMainLanguage(true) .'/'. str_replace(' ', '_', $this->getWord());
        return $full ? URL::to($path) : $path;
    }

    public function getEditUri($full = true) {
        return route('definition.edit', ['id' => $this->getId()], $full);
    }
}

