<?php
namespace Midata;

use Midata\Midata;
use Midata\Table;
use Midata\View;
use Midata\DDL as MidataDDL;
use Midata\DML as MidataDML;
use Midata\DML\Conditional\Select as MidataSelect;

// mysql
use Midata\Adapter\Pdo\Mysql as MysqlAdapter;
use Midata\Mysql\View as MysqlView;
use Midata\Mysql\Table as MysqlTable;

abstract class Adapter extends Midata
{
    const ADAPTER_MYSQL = 'mysql';

    /**
     * @var string $name Name of adapter.
     */
    private $name;

    /**
     * @var array $config Config of adapter.
     */
    private $config;

    private static $adapters = array(
        self::ADAPTER_MYSQL => MysqlAdapter::class,
    );

    /**
     * Execute any query to adapter, and return result dependent from adapter.
     *
     * @param mixed $query The adapter should provides capability to execute some
     * command like sql or other command dependent from adapter.
     */
    abstract public function execute($query);

    /**
     * Return list of tables.
     * @return array
     */
    abstract public function tables();

    /**
     * Return list of views.
     * @return array
     */
    abstract public function views();

    public static function factory($adapter, $config, $name = null)
    {
        $assert = static::service('assert');
        $assert->hasIndex(self::$adapters, $adapter, "The adapter $adapter is not supported.");

        // pobieram informacje o konstruktorze
        $adapter = self::$adapters[$adapter];

        // tworze adapter
        $adapter = new $adapter($config);

        if (!is_null($name)) {
            // ustawiam nazwe dla adaptera
            $adapter->name($name);
        }

        return $adapter;
    }

    function __construct($config)
    {
        $this->config = $config;
    }

    public function config($key)
    {
        $util = $this->service('util');

        if (!$util->arrayHas($this->config, $key)) {
            return null;
        }

        return $this->config[$key];
    }

    public function name($name = null)
    {
        if (is_null($name)) {
            return $this->name;
        }

        $this->name = $name;
        return $this;
    }

    /**
     * Return instace of Midata\Table for specific adapter and table.
     */
    public function table($name)
    {
        $assert = $this->service('assert');
        $class = get_class($this);

        switch ($class) {
        case MysqlAdapter::class:
            $table = new MysqlTable();
            break;
        default:
            $assert->exception("The table for $class is not supported.");
            break;
        }

        $table
            ->adapter($this)
            ->name($name)
        ;

        return $table;
    }

    /**
     * Return instace of Midata\Table for specific adapter and table.
     */
    public function view($name)
    {
        $class = get_class($this);

        switch ($class) {
        case MysqlAdapter::class:
            $view = new MysqlView();
            break;

        default:
            $assert->exception("View is not supported for adapter $class.");
            break;
        }

        $view->adapter($this);
        $view->name($name);

        return $view;
    }

    abstract public function fetchAll(MidataSelect $select);
    abstract public function fetchRow(MidataSelect $select);
    abstract public function fetchCol(MidataSelect $select);
    abstract public function fetchOne(MidataSelect $select);
    abstract public function count(MidataSelect $select);

    /**
     * Return instace of DDL (Data Definition Builder) for specificed type of
     * object.
     *
     * @param string $statement column|trigger|foreignKey|view|table
     * @return \Midata\DDL
     */
    public function ddl($statement)
    {
        return MidataDDL::factory($this, $statement);
    }

    /**
     * Return instace of DML (Data Manipulation Language) for specificed type of
     * object.
     *
     * @param string $statement insert|delete|update|select
     * @return \Midata\DML
     */
    public function dml($statement)
    {
        return MidataDML::factory($this, $statement);
    }

    /**
     * Check if adapter is specified type.
     *
     * @return bool
     */
    public function isMysql()
    {
        if ($this instanceof MysqlAdapter) {
            return true;
        }

        return false;
    }
}
