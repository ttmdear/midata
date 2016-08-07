<?php
namespace Midata\DML;

use Midata\DML as MidataDML;

class Insert extends MidataDML
{
    private $table;
    private $values = array();

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
        $assert = $this->service('assert');

        if (is_null($values)) {
            return $this->values;
        }

        $assert->isArray($values, "Values should be array.");

        foreach ($values as $name => $value) {
            $this->value($name, $value);
        }

        return $this;
    }

    public function value($name, $value)
    {
        $this->values[$name] = $value;
        return $this;
    }

    public function reset()
    {
        $this->values = array();
        return $this;
    }

    public function sql()
    {
        $table = $this->table();
        $values = $this->values();

        if(is_null($table) || empty($values) || !is_array($values)){
            $this->exception("The insert statement must have define table and values which should be array.");
        }

        $sql = "INSERT INTO `$table` (";
        foreach (array_keys($values) as $column) {
            $sql .= "`$column`,";
        }

        $sql = rtrim($sql, ',');
        $sql .= ") VALUES (";

        foreach ($values as $value) {
            $value = $this->quote($value);
            $sql .= "$value,";
        }

        $sql = rtrim($sql, ',');
        $sql .= ")";

        return $sql;
    }
}
