<?php namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

/**
 * Base class for Di Nkomo objects.
 */
abstract class BaseResource extends Model {

    /**
     * Alternate spellings
     */
    public $altSpellings    = false;
    
    /**
     * Other JSON objects to process
     */
    public $jsonObjects     = false;

    protected static $obfuscator;
    
    /**
     * List of main spellings
     */
    protected $_altMain     = [];
    
    /**
     * List of arrays containing alternate spellings
     */
    protected $_altOthers   = [];
    
    protected $_params      = [];
    protected $_jsons       = [];
    protected $_encodedId;
    protected $isOrganized  = false;
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Save instance of Obfuscator within our object.
        //$this->obfuscator = App::make('Obfuscator');
    }

    public static function getObfuscator()
    {
        if (!isset(static::$obfuscator)) {
            static::$obfuscator = App::make('Obfuscator');
        }

        return static::$obfuscator;
    }

    public function organize()
    {
        if (!$this->isOrganized)
        {
            // Parameters
            $this->_params  = (array) json_decode($this->params);
            
            // Other JSON objects
            if ($this->jsonObjects)
            {
                foreach ($this->jsonObjects as $prop)
                {
                    $this->_jsons[$prop]    = (array) json_decode($this->{$prop});
                }
            }

            // Alternate spellings
            if ($this->altSpellings)
            {
                foreach ($this->altSpellings as $prop)
                {
                    // Create array of spellings
                    $spellings = explode(',', (string)$this->{$prop});
                    
                    // Retrieve main spelling, save it in main list
                    $this->_altMain[$prop] = trim(array_shift($spellings));
                    
                    // Initialize list of alternate spellings
                    $this->_altOthers[$prop] = array();
                    
                    // Store alternate spellings inside an array
                    if (count($spellings)) {
                        foreach ($spellings as $spelling) {
                            $this->_altOthers[$prop][] = trim($spelling);
                        }
                    }
                }
            }
            
            // 
            $this->isOrganized = true;
        }
        
        return $this;
    }
    
    public function getMainAlt($prop) {
        return $this->organize()->_altMain[$prop];
    }
    
    public function setMainAlt($prop, $to)
    {
        $this->organize();
        
        // Do a bit of cleaning
        $to     = trim(strip_tags((string) $to));
        $to     = preg_replace('/\n\r/', '', $to);
        if (strlen($to) < 2) {
            return false;
        }
        
        $this->_altMain[$prop] = $to;
        
        return true;
    }

    public function getOtherAlts($prop, $toArray = false) {
        $this->organize();
        return $toArray ? $this->_altOthers[$prop] : implode(', ', $this->_altOthers[$prop]);
    }
    
    public function setOtherAlt($prop, $alt)
    {
        $this->organize();
        
        // Arrays
        $alt    = strpos($alt, ',') !== false ? explode(',', $alt) : $alt;
        if (is_array($alt))
        {
            // Override array
            $this->_altOthers[$prop] = array();
            foreach ($alt as $alternate) {
                $this->setOtherAlt($prop, $alternate);
            }
            
            return count($this->_altOthers[$prop]);
        }
        
        // Do a bit of cleaning
        $alt    = trim(strip_tags((string) $alt));
        $alt    = preg_replace('/\n\r/', '', $alt);
        if (strlen($alt) < 2) {
            return count($this->_altOthers[$prop]);
        }
		
		// Check if alternate is already present
		if (in_array($alt, $this->_altOthers[$prop])) {
			return count($this->_altOthers[$prop]);
		}
		
		// Add alternate spelling, sort alphabetically, and return number of spellings
        $this->_altOthers[$prop][] = $alt;
		sort($this->_altOthers[$prop], SORT_REGULAR);
		
        return count($this->_altOthers[$prop]);
    }
    
    public function unsetOtherAlt($prop, $alt)
    {
        if (in_array($alt, $this->_altOthers[$prop]))
        {
            foreach ($this->_altOthers[$prop] as $key => $value)
            {
                if ($value == $alt) {
                    unset($this->_altOthers[$prop][$key]);
                    break;
                }
            }
        }

        return true;
    }

    /**
     * Retrieves a parameter value.
     * @param  string   $key
     * @param  mixed    $def
     * @return mixed
     */
    public function getParam($key, $def = null) {
        $this->organize();
        return isset($this->_params[$key]) ? $this->_params[$key] : $def;
    }
    
    /**
     * Sets a new value for a parameter
     * @param string    $key
     * @param mixed     $value
     * @return mixed            The old value for this parameter.
     */
    public function setParam($key, $value = null)
    {
        $this->organize();
        
        // Set new value, return old value
        $old    = @$this->_params[$key];
        $this->_params[$key]    = $value;

        return $old;
    }
    
    public function getJsonParam($prop, $key, $def = null) {
        $this->organize();
        return isset($this->_jsons[$prop][$key]) ? $this->_jsons[$prop][$key] : $def;
        //return property_exists($this->_jsons[$prop], $key) ? $this->_jsons[$prop]->$key : $def;
    }
    
    public function setJsonParam($prop, $key, $value = null)
    {
        $this->organize();
        
        // Set new value, return old value
        $old    = @$this->_jsons[$prop][$key];
        $this->_jsons[$prop][$key] = $value;

        return $old;
    }
    
    public function getId()
    {
        if (is_null($this->_encodedId)) {
            //$this->_encodedId   = $this->id > 0 ? $this->obfuscator->encode($this->id) : 0;
            $this->_encodedId   = $this->id > 0 ? static::getObfuscator()->encode($this->id) : 0;
        }
        
        return $this->_encodedId;
    }

	/**
	 * Find a model by its primary key.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return \Illuminate\Support\Collection|static|null
	 */
	public static function find($id, $columns = ['*'])
	{
        // Un-obfuscate ID
        if (is_string($id) && strlen($id) >= 8 && !is_numeric($id)) {
            $id = static::getObfuscator()->decode($id)[0];
        }
        
        return parent::find($id, $columns);
	}
    public static function findOrNew($id, $columns = ['*'])
    {
        // Un-obfuscate ID
        if (is_string($id) && strlen($id) >= 8 && !is_numeric($id)) {
            $id = static::getObfuscator()->decode($id)[0];
        }

        return parent::findOrNew($id, $columns);
    }
    public static function findOrFail($id, $columns = ['*'])
    {
        // Un-obfuscate ID
        if (is_string($id) && strlen($id) >= 8 && !is_numeric($id)) {
            $id = static::getObfuscator()->decode($id)[0];
        }

        return parent::findOrFail($id, $columns);
    }

    public static function boot()
    {
        parent::boot();

        // 
        self::saving(function($obj)
        {
            // Performance check
            if (!$obj->isOrganized) {
                return true;
            }
            
            // Convert params string
            $obj->params = json_encode($obj->_params);
            
            // Encode extra parameters
            if ($obj->jsonObjects)
            {
                foreach ($obj->jsonObjects as $prop)
                {
                    $obj->{$prop}   = json_encode($obj->_jsons[$prop]);
                }
            }

            // Combine main, alternate spellings
            if ($obj->altSpellings)
            {
                foreach ($obj->altSpellings as $prop)
                {
                    $obj->{$prop} = $obj->_altMain[$prop];
                    
                    if (count($obj->_altOthers[$prop])) {
                        $obj->{$prop} .= ', '. implode(', ', $obj->_altOthers[$prop]);
                    }
                }
            }

            return true;
        });
    }
}
