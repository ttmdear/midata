<?php
namespace Midata\Object;

// mysql
use Midata\Mysql\Table as MysqlTable;
use Midata\Mysql\Column as MysqlColumn;
use Midata\Mysql\Trigger as MysqlTrigger;
use Midata\Mysql\Index as MysqlIndex;
use Midata\Mysql\Constraint as MysqlConstraint;

// midata
use Midata\Object;

/**
 * This is class represents the database table.
 */
abstract class Table extends Object
{
    /**
     * Returns a list of all columns in the table.
     *
     * @return array
     */
    abstract public function columns();

    /**
     * Returns a list of all indexes in the table.
     *
     * @param bool $all Some indexes are created as result creating other
     * object like constraint and primary key. Normally this function omits
     * these indexes because they are created automaticly. But if you want to,
     * get list of all indexes, enter "true" value to "all" variable.
     * @return array
     */
    abstract public function indexes($all = false);

    /**
     * Returns a list of all the columns that are part of the primary key .
     *
     * @return array
     */
    public function primaryKey()
    {
        $constraints = $this->constraints();

        foreach ($constraints as $constraint) {
            $constraint = $this->constraint($constraint);

            if ($constraint->isPrimaryKey()) {
                return $constraint->columns();
            }
        }

        return array();
    }


    /**
     * Returns a list of all triggers in the table.
     *
     * @return array
     */
    abstract public function triggers();

    /**
     * Returns a list of all constraints in the table.
     *
     * @return array
     */
    abstract public function constraints();

    public static function allAttributes()
    {
        return array();
    }

    /**
     * Returns the column with the specified name.
     *
     * @param string $name
     * @return \Midata\Column
     */
    public function column($name)
    {
        $assert = static::service('assert');
        $class = get_class($this);

        switch ($class) {
        case MysqlTable::class:
            $column = new MysqlColumn();
            break;

        default:
            $assert->exception("The column for $class is not supported.");
            break;
        }

        $column
            ->table($this)
            ->name($name)
        ;

        return $column;
    }

    /**
     * Returns the trigger with the specified name.
     *
     * @param string $name
     * @return \Midata\Trigger
     */
    public function trigger($name)
    {
        $assert = static::service('assert');
        $class = get_class($this);

        switch ($class) {
        case MysqlTable::class:
            $trigger = new MysqlTrigger();
            break;

        default:
            $assert->exception("The trigger for $class is not supported.");
            break;
        }

        $trigger
            ->table($this)
            ->name($name)
        ;

        return $trigger;
    }

    /**
     * Returns the index with the specified name.
     *
     * @param string $name
     * @return \Midata\Trigger
     */
    public function index($name)
    {
        $assert = static::service('assert');
        $class = get_class($this);

        switch ($class) {
        case MysqlTable::class:
            $index = new MysqlIndex();
            break;

        default:
            $assert->exception("The Index for $class is not supported.");
            break;
        }

        $index
            ->table($this)
            ->name($name)
        ;

        return $index;
    }

    /**
     * Returns the constraint with the specified name.
     *
     * @param string $name
     * @return \Midata\Constraint
     */
    public function constraint($name)
    {
        $assert = static::service('assert');
        $class = get_class($this);

        switch ($class) {
        case MysqlTable::class:
            $constraint = new MysqlConstraint();
            break;

        default:
            $assert->exception("The Constraint for $class is not supported.");
            break;
        }

        $constraint
            ->table($this)
            ->name($name)
        ;

        return $constraint;
    }

}
