<?php
namespace Midata\Mysql\DDL;

use Midata\DDL\Trigger as DDLTrigger;
use Midata\Object\Trigger as MidataTrigger;

class Trigger extends DDLTrigger
{
    public function create(MidataTrigger $trigger)
    {
        $name = $trigger->name();
        $table = $trigger->tableName();

        // INSERT, DELETE, UPDATE
        $event = $trigger->event();

        // AFTER, BEFORE
        $timming = $trigger->timming();

        // tresc triggera
        $statement = $trigger->statement();

        // kiedy trigger ma byc wykonany
        $orientation = $trigger->orientation();
        $orientation = "FOR EACH $orientation";

        $sql = "DELIMITER $$\nCREATE TRIGGER `$name`\n    $timming $event on `$table`\n $orientation\n$statement\n$$\nDELIMITER ;";

        return $sql;
    }

    public function drop(MidataTrigger $trigger)
    {
        $name = $trigger->name();

        return "DROP TRIGGER IF EXISTS `$name`;";
    }
}
