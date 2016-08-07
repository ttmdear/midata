<?php
namespace Midata;

use Midata\Midata;
use Midata\Object\Table as MidataTable;
use Midata\Adapter;

/**
 * This is class represents the database object. All database objects are
 * affiliated with the adapter. Each object has its own name and większność is
 * linked to the table .
 */
abstract class Object extends Midata
{
    /**
     * Methods associated with attributes can operate in two modes , Set and
     * Get . Stala Getter determines the mode in which no act method . This
     * constant is used because you can not use NULL which is also used in the
     * database.
     */
    CONST GETTER = '73dace817ffbd25f35bbde4a133d746e02c46337';

    /**
     * If the object does not support attribute , then it returns the value "
     * NOT_SUPPORTED"
     */
    CONST NOT_SUPPORTED = '73dace817ffbd25f35bbde4a133d746e02c46336';

    /**
     * Name of database object.
     */
    private $name;

    /**
     * Table related with object.
     *
     * @var \Midata\Table $table Reference to related table.
     */
    private $table;

    /**
     * Adapter related with object.
     *
     * @var \Midata\Adapter $adapter Reference to adapter of object.
     */
    private $adapter;

    /**
     * Overwritten object attributes.
     * @var array $overwrite
     */
    private $overwrite = array();

    /**
     * Each object has its own attributes , which can be listed by
     * allAttributes method . Each of these features should be implemented by
     * method with the same name.
     */
    abstract public static function allAttributes();

    /**
     * Sets or returns the database adapter.
     *
     * @param \Midata\Adapter $adapter
     * @return \Midata\Adapter|self
     */
    public function adapter(Adapter $adapter = null)
    {
        if (is_null($adapter)) {
            $assert = $this->service('assert');
            $adapter = null;

            if (is_null($this->adapter)) {
                if (!is_null($this->table)) {
                    // get adapter from table is table is sed
                    $adapter = $this->table->adapter();
                }else{
                    $adapter = $this->adapter;
                }
            }else{
                $adapter = $this->adapter;
            }

            $assert->notNull($adapter, "Adapter is not set for object.");

            return $adapter;
        }

        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Return database schema (dbname) of object.
     *
     * @return string
     */
    public function schema()
    {
        $assert = $this->service('assert');

        $adapter = $this->adapter();
        $schema = $adapter->config('dbname', null);

        $assert->notNull($schema, "Please define dbname for {$adapter->name()}");

        return $schema;
    }

    /**
     * Sets or returns the name of database object.
     *
     * @param string $name
     * @return string
     */
    public function name($name = null)
    {
        $assert = $this->service('assert');

        if (is_null($name)) {
            $assert->notNull($this->name, "The object has not defined name.");
            return $this->name;
        }

        $assert->isString($name);
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the table associated with the object .
     *
     * @return string|null
     */
    public function tableName()
    {
        return $this->table()->name();
    }

    /**
     * Sets or returns table object associated with database object.
     *
     * @param \Midata\Table $table
     * @return \Midata\Table|string
     */
    public function table(MidataTable $table = null)
    {
        $assert = $this->service('assert');

        if (is_null($table)) {
            $assert->notNull($this->table, "The object has not defined associated table.");
            return $this->table;
        }

        $this->table = $table;

        return $this;
    }

    /**
     * Delegation of executute adapter method to inner use.
     *
     * @param mixed $sql
     * @return mixed
     */
    protected function execute($sql)
    {
        return $this->adapter()->execute($sql);
    }

    /**
     * Return value of object attribute, if attribute is not supported then
     * exception will be thrown.
     *
     * @param string $attribute
     * @return mixed
     */
    public function need($attribute)
    {
        $assert = $this->service('assert');

        if (!$this->isSupported($attribute)) {
            $assert->exception("The {$attribute} is need but not supported.");
        }

        return $this->$attribute();
    }

    /**
     * Return value of object attribute, if attribute is not supported then
     * exception will be thrown.
     *
     * @param string $attribute
     * @return mixed
     */
    public function isSupported($attribute)
    {
        $assert = $this->service('assert');

        if (!method_exists($this, $attribute)) {
            $assert->exception("$attribute has not implementation at ".get_class($this));
        }

        $value = $this->$attribute();

        if ($value === self::NOT_SUPPORTED) {
            return false;
        }

        return true;
    }

    protected function overwrite($attribute, $value)
    {
        if($value !== self::GETTER){
            $this->overwrite[$attribute] = $value;
        }

        return $this;
    }

    protected function isOverwrite($attribute)
    {
        if (in_array($attribute, array_keys($this->overwrite))) {
            return true;
        }

        return false;
    }

    protected function getOverwrite($attribute)
    {
        $assert = $this->service('assert');

        if (!$this->isOverwrite($attribute)) {
            $assert->exception("The attribute is not overwrited");
        }

        return $this->overwrite[$attribute];
    }
}
