<?php
namespace Midata\Mysql;

use Midata\Object\Table as MidataTable;
use Midata\Mysql\Constraint as MysqlConstraint;

/**
 * It is the object representing Mysql tables .
 */
class Table extends MidataTable
{
    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $table = $this->name();

        $indexex = $this->execute("SHOW COLUMNS FROM  $table");
        $columns = array();

        foreach ($indexex as $index) {
            $columns[] = $index['Field'];
        }

        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function triggers()
    {
        $schema = $this->schema();
        $table = $this->name();

        $sql = "
            SELECT
                t.TRIGGER_NAME
            FROM information_schema.TRIGGERS t
            where t.TRIGGER_SCHEMA = '$schema'
            AND t.EVENT_OBJECT_TABLE = '$table'
        ";

        $result = $this->execute($sql);

        $triggers = array();

        foreach ($result as $row) {
            $triggers[] = $row['TRIGGER_NAME'];
        }

        return $triggers;
    }

    /**
     * {@inheritDoc}
     */
    public function indexes($all = false)
    {
        $schema = $this->schema();
        $tableName = $this->name();
        $constraints = $this->constraints();

        $sql = "
            SELECT DISTINCT
                INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = '$schema'
            and TABLE_NAME = '$tableName'
        ";

        $result = $this->execute($sql);

        if (empty($result)) {
            return array();
        }

        $indexes = array();
        foreach ($result as $row) {
            if (!in_array($row['INDEX_NAME'], $constraints) || $all) {
                $indexes[] = $row['INDEX_NAME'];
            }
        }

        return $indexes;
    }

    /**
     * {@inheritDoc}
     */
    public function constraints()
    {
        $schema = $this->schema();
        $table = $this->name();

        $sql = "
            select
                CONSTRAINT_NAME,
                CONSTRAINT_TYPE
            from information_schema.table_constraints
            where TABLE_SCHEMA = '$schema'
            and TABLE_NAME = '$table'
        ";

        $result = $this->execute($sql);
        $constraints = array();

        foreach ($result as $row) {
            if(!in_array($row['CONSTRAINT_TYPE'], array(MysqlConstraint::CONSTRAINT_PRIMARY, MysqlConstraint::CONSTRAINT_FOREIGN_KEY))){
                continue;
            }

            $constraints[] = $row['CONSTRAINT_NAME'];
        }

        return $constraints;
    }
}
