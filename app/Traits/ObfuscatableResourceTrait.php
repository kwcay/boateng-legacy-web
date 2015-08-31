<?php namespace App\Traits;

use App;

trait ObfuscatableResourceTrait
{
    /**
     * @var
     */
    protected static $obfuscator;

    /**
     * @return mixed
     */
    public static function getObfuscator()
    {
        if (!isset(static::$obfuscator)) {
            static::$obfuscator = App::make('Obfuscator');
        }

        return static::$obfuscator;
    }

    /**
     * @return int|string   Obfuscated ID, or 0.
     */
    public function getId()
    {
        if (is_null($this->_encodedId)) {
            $this->_encodedId   = $this->id > 0 ? static::getObfuscator()->encode($this->id) : 0;
        }

        return $this->_encodedId;
    }

    /**
     * Find a model by its primary key.
     *
     * @param int|string $id
     * @param array $columns
     * @return \Illuminate\Support\Collection|static|null
     */
    public static function find($id, $columns = ['*'])
    {
        // Un-obfuscate ID
        if (is_string($id) && !is_numeric($id) && strlen($id) >= 8) {
            $id = static::getObfuscator()->decode($id)[0];
        }

        return static::query()->find($id, $columns);
    }

    /**
     * @param int|string $id
     * @param array $columns
     * @return mixed
     */
    public static function findOrNew($id, $columns = ['*'])
    {
        // Un-obfuscate ID
        if (is_string($id) && !is_numeric($id) && strlen($id) >= 8) {
            $id = static::getObfuscator()->decode($id)[0];
        }

        return parent::findOrNew($id, $columns);
    }

    /**
     * @param int|string $id
     * @param array $columns
     * @return mixed
     */
    public static function findOrFail($id, $columns = ['*'])
    {
        // Un-obfuscate ID
        if (is_string($id) && !is_numeric($id) && strlen($id) >= 8) {
            $id = static::getObfuscator()->decode($id)[0];
        }

        return parent::findOrFail($id, $columns);
    }
}
