<?php
namespace Midata;

use Midata\Midata;
use Midata\Object as MidataObject;

// mysql
use Midata\Adapter\Pdo\Mysql as MysqlAdapter;
use Midata\Mysql\DDL\Column as BuilderMysqlColumn;
use Midata\Mysql\DDL\Table as BuilderMysqlTable;
use Midata\Mysql\DDL\Constraint as BuilderMysqlConstraint;
use Midata\Mysql\DDL\Trigger as BuilderMysqlTrigger;
use Midata\Mysql\DDL\View as BuilderMysqlView;
use Midata\Mysql\DDL\Index as BuilderMysqlIndex;

abstract class DDL extends Midata
{
    const COLUMN = 'column';
    const TABLE = 'table';
    const CONSTRAINT = 'constraint';
    const TRIGGER = 'trigger';
    const VIEW = 'view';
    const INDEX = 'index';

    /**
     * Each object is related with adapter.
     *
     * @var \Midata\Adapter $adapter Reference to adapter of object.
     */
    private $adapter;

    private static $mapOfClass = array(
        MysqlAdapter::class => array(
            self::COLUMN => BuilderMysqlColumn::class,
            self::TABLE => BuilderMysqlTable::class,
            self::CONSTRAINT => BuilderMysqlConstraint::class,
            self::TRIGGER => BuilderMysqlTrigger::class,
            self::VIEW => BuilderMysqlView::class,
            self::INDEX => BuilderMysqlIndex::class,
        )
    );

    public function alter(MidataObject $object)
    {
        $drop = $this->drop($object);
        $create = $this->create($object);

        return "$drop\n$create";
    }

    public static function factory($adapter, $statement)
    {
        $assert = static::service('assert');
        $class = get_class($adapter);

        if (!isset(self::$mapOfClass[$class])) {
            $assert->exception("There are no supported of DDLBuilder for $class adapter.");
        }

        $classes = self::$mapOfClass[$class];

        if (!isset($classes[$statement])) {
            $assert->exception("There are no supported of DDLBuilder $statement for $class adapter.");
        }

        $ddlBuilder = $classes[$statement];
        $ddlBuilder = new $ddlBuilder();
        $ddlBuilder->adapter($adapter);

        return $ddlBuilder;
    }

    /**
     * Sets or returns the database adapter.
     *
     * @param \Midata\Adapter $adapter
     * @return \Midata\Adapter|self
     */
    public function adapter(Adapter $adapter = null)
    {
        if (is_null($adapter)) {
            if (is_null($this->adapter)) {
                if (!is_null($this->table)) {
                    return $this->table->adapter();
                }else{
                    return $this->adapter;
                }
            }else{
                return $this->adapter;
            }
        }

        $this->adapter = $adapter;

        return $this;
    }

    protected function implode($array, $delimiter = ',', $covering = "`")
    {
        $assert = $this->service('assert');
        $assert->isArray($array);

        $imploded = "";

        foreach ($array as $value) {
            $imploded .= "$covering$value$covering,";
        }

        $imploded = trim($imploded, ',');

        return $imploded;
    }
}
