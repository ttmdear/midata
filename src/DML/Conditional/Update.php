<?php
namespace Midata\DML\Conditional;

use Midata\DML\Conditional as DMLConditional;

class Update extends DMLConditional
{
    protected $table;
    protected $values = array();

    public function table($table = null)
    {
        if (!is_null($table)) {
            $this->table = $table;
            return $this;
        }

        return $this->table;
    }

    public function values($values = null)
    {
        if (is_null($values)) {
            return $this->values;
        }

        $this->values = $values;
        return $this;
    }

    public function sql()
    {
        $assert = $this->service('assert');

        $table = $this->table();
        $values = $this->values();
        $where = $this->where();

        if(is_null($table)){
            $assert->exception("The table must be define $table");
        }

        if (empty($values)) {
            $assert->exception("The must be define at least one value to update.");
        }

        $sql = "UPDATE `$table`\n";

        if (!empty($values)) {
            $sql .= 'SET ';
            foreach ($values as $column => $value) {
                $sql .= "`$column` = ".$this->quote($value).',';
            }
        }

        $sql = rtrim($sql, ',');

        if (!is_null($where)) {
            $sql .= "\nWHERE ".$where;
        }

        $sql = $this->processSql($sql);

        return $sql;
    }
}
