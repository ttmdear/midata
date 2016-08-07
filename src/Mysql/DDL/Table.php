<?php
namespace Midata\Mysql\DDL;

use Midata\DDL\Table as DDLTable;
use Midata\Object\Table as MidataTable;
use Midata\DDL as MidataDDL;

class Table extends DDLTable
{
    public function create(MidataTable $table)
    {
        // columns
        $ddlColumn = MidataDDL::factory($table->adapter(), MidataDDL::COLUMN);

        $tableName = $table->name();
        $sql = "CREATE TABLE `$tableName`(";

        $name = $table->column("name");
        foreach ($table->columns() as $column) {
            $column = $table->column($column);
            $inline = $ddlColumn->inline($column);

            $sql = "$sql\n    $inline,";
        }

        // constraints
        $ddlConstraint = MidataDDL::factory($table->adapter(), MidataDDL::CONSTRAINT);
        foreach ($table->constraints() as $constraint) {
            $constraint = $table->constraint($constraint);
            $inline = $ddlConstraint->inline($constraint);

            $sql = "$sql\n    $inline,";
        }

        // indexes
        $ddlIndex = MidataDDL::factory($table->adapter(), MidataDDL::INDEX);
        foreach ($table->indexes() as $index) {
            $index = $table->index($index);
            $inline = $ddlIndex->inline($index);

            $sql = "$sql\n    $inline,";
        }

        $sql = trim($sql, ',');
        $sql .= "\n)";

        return "$sql;";
    }

    public function drop(MidataTable $table)
    {
        $tableName = $table->name();
        $sql = "DROP TABLE IF EXISTS `$tableName`";

        return "$sql;";
    }
}
