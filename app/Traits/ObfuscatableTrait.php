<?php namespace App\Traits;

use App;

trait ObfuscatableTrait
{
    /**
     * @var
     */
    protected static $obfuscator;

    /**
     * @var
     */
    protected $_id;

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
    public function getUniqueId()
    {
        if (is_null($this->_id)) {
            $this->_id   = $this->id > 0 ? static::getObfuscator()->encode($this->id) : 0;
        }

        return $this->_id;
    }

    /**
     * @return int|string   Obfuscated ID, or 0.
     *
     * @deprecated
     */
    public function getId() {
        return $this->getUniqueId();
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
        if (is_string($id) && !is_numeric($id) && strlen($id) >= 8)
        {
            if ($decoded = static::getObfuscator()->decode($id)) {
                $id = $decoded[0];
            }

            else {
                return null;
            }
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
