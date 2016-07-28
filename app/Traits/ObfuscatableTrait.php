<?php

namespace App\Traits;

use App;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

trait ObfuscatableTrait
{
    /**
     * @var
     */
    protected static $obfuscator;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @return mixed
     */
    public static function getObfuscator()
    {
        if (! isset(static::$obfuscator)) {
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
            $this->obfuscatorId = $this->obfuscatorId ?: 3;
            $this->_id = $this->id > 0 ?
                static::getObfuscator()->encode($this->obfuscatorId, $this->id) : 0;
        }

        return $this->_id;
    }

    /**
     * Accessor for $this->uniqueId.
     */
    public function getUniqueIdAttribute()
    {
        return $this->getUniqueId();
    }

    /**
     * Decodes an ID.
     *
     * @param int|string $encodedId
     * @return int|null
     */
    public static function decodeId($encodedId)
    {
        $id = 0;

        // Un-obfuscate ID
        if (is_string($encodedId) && ! is_numeric($encodedId) && strlen($encodedId) >= 8) {
            if ($decoded = static::getObfuscator()->decode($encodedId)) {
                $id = $decoded[1];
            } else {
                $id = null;
            }
        } elseif (is_numeric($encodedId)) {
            $id = (int) $encodedId;
        }

        return $id;
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
        if ($id = static::decodeId($id)) {
            return static::query()->find($id, $columns);
        }
    }

    /**
     * Find a soft-deleted model by its primary key.
     *
     * @param int|string $id
     * @param array $columns
     * @return \Illuminate\Support\Collection|static|null
     */
    public static function findTrashed($id, $columns = ['*'])
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive(get_called_class()))) {
            throw new Exception(get_called_class().' does not soft-delete.');
        }

        if ($id = static::decodeId($id)) {
            return static::onlyTrashed()->find($id, $columns);
        }
    }

    /**
     * @param int|string $id
     * @param array $columns
     * @return mixed
     */
    public static function findOrNew($id, $columns = ['*'])
    {
        // Un-obfuscate ID
        if (is_string($id) && ! is_numeric($id) && strlen($id) >= 8) {
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
        if (is_string($id) && ! is_numeric($id) && strlen($id) >= 8) {
            $id = static::getObfuscator()->decode($id)[0];
        }

        return parent::findOrFail($id, $columns);
    }
}
