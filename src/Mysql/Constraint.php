<?php
namespace Midata\Mysql;

use Midata\Object\Constraint as MysqlConstraint;

class Constraint extends MysqlConstraint
{
    const RESTRICT = 'RESTRICT';
    const CASCADE = 'CASCADE';
    const SET_NULL = 'SET NULL';
    const NO_ACTION = 'NO_ACTION';

    const CONSTRAINT_FOREIGN_KEY = 'FOREIGN KEY';
    const CONSTRAINT_PRIMARY = 'PRIMARY KEY';

    private $data;

    public function baseTable()
    {
        return $this->get('table');
    }

    public function refTable()
    {
        return $this->get('refTable');
    }

    public function onDelete()
    {
        return $this->get('onDelete');
    }

    public function onUpdate()
    {
        return $this->get('onUpdate');
    }

    public function isPrimaryKey()
    {
        if ($this->get('constraintType') === self::CONSTRAINT_PRIMARY) {
            return true;
        }

        return false;
    }

    public function isForeignKey()
    {
        if ($this->get('constraintType') === self::CONSTRAINT_FOREIGN_KEY) {
            return true;
        }

        return false;
    }

    public function columns()
    {
        $assert = $this->service('assert');

        if ($this->isPrimaryKey()) {

            $references = $this->get('columns');

            $columns = array();
            foreach ($references as $reference) {
                $columns[] = $reference['COLUMN_NAME'];
            }

            return $columns;

        }elseif($this->isForeignKey()){
            $references = $this->get('columns');

            $columns = array();
            foreach ($references as $reference) {
                $column = array(
                    'base' => $reference['COLUMN_NAME'],
                    'ref' => $reference['REFERENCED_COLUMN_NAME'],
                );

                $columns[] = $column;
            }

            return $columns;
        }else{
            $assert->exception("Wrong type of constraint");
        }
    }

    private function get($attribute)
    {
        $assert = $this->service('assert');

        if (is_null($this->data)){
            // data not loaded
            $schema = $this->schema();
            $tableName = $this->tableName();
            $constraintName = $this->name();

            // First, I determine type of constraint.
            $sql = "
                select
                    CONSTRAINT_NAME,
                    CONSTRAINT_TYPE
                from information_schema.table_constraints
                where TABLE_SCHEMA = '$schema'
                and TABLE_NAME = '$tableName'
                and CONSTRAINT_NAME = '$constraintName';
            ";

            $result = $this->execute($sql);

            if (empty($result)) {
                $assert->exception("There are no metadata about $name constraint.");
            }

            $result = $result[0];

            $this->data['constraintType'] = $result['CONSTRAINT_TYPE'];

            if ($this->data['constraintType'] === self::CONSTRAINT_FOREIGN_KEY) {
                $this->loadForeignKey();
            }

            // loaded column
            $sql = "
            select
                COLUMN_NAME,
                REFERENCED_COLUMN_NAME
            from
                information_schema.key_column_usage
            where
                CONSTRAINT_SCHEMA = '$schema'
                AND TABLE_NAME = '$tableName'
                and CONSTRAINT_NAME = '$constraintName'
            ";

            $references = $this->execute($sql);

            if (empty($result)) {
                $assert->exception("There are no metadata about $name constraint.");
            }

            $this->data['columns'] = $references;
        }

        return $this->data[$attribute];
    }

    private function loadForeignKey()
    {
        $schema = $this->schema();
        $tableName = $this->tableName();
        $constraintName = $this->name();

        $sql = "
        select
            UPDATE_RULE,
            DELETE_RULE,
            TABLE_NAME,
            REFERENCED_TABLE_NAME
        from information_schema.referential_constraints
        where
            CONSTRAINT_SCHEMA = '$schema'
            AND TABLE_NAME = '$tableName'
            AND CONSTRAINT_NAME = '$constraintName'
        ";

        $references = $this->execute($sql);

        if (empty($references)) {
            $assert->exception("There are referential constraints $constraintName for $tableName.");
        }

        $reference = $references[0];

        $this->data['table'] = $reference['TABLE_NAME'];
        $this->data['refTable'] = $reference['REFERENCED_TABLE_NAME'];
        $this->data['onUpdate'] = $reference['UPDATE_RULE'];
        $this->data['onDelete'] = $reference['DELETE_RULE'];

    }
}
