<?php
namespace Midata\DML\Conditional;

use Midata\DML\Conditional as DMLConditional;

class Delete extends DMLConditional
{
    private $table;

    public function table($table = null)
    {
        if (!is_null($table)) {
            $this->table = $table;
            return $this;
        }

        return $this->table;
    }

    public function sql()
    {
        $assert = $this->service('assert');

        $table = $this->table();
        $where = $this->where();

        if(is_null($table)){
            $assert->exception("Delete statement must be defined table.");
        }

        $sql = "DELETE FROM `$table`\n";

        if (!is_null($where)) {
            $sql .= "WHERE $where";
        }

        $sql = $this->processSql($sql);

        return $sql;
    }
}
