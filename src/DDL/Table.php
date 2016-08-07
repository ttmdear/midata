<?php
namespace Midata\DDL;

use Midata\DDL as MidataDDL;
use Midata\Object\Table as MidataTable;

abstract class Table extends MidataDDL
{
    abstract public function create(MidataTable $table);
    abstract public function drop(MidataTable $table);
}
