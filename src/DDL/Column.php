<?php
namespace Midata\DDL;

use Midata\DDL as MidataDDL;
use Midata\Object\Column as MidataColumn;

abstract class Column extends MidataDDL
{
    abstract public function create(MidataColumn $column);
    abstract public function drop(MidataColumn $column);
    abstract public function inline(MidataColumn $column);
}
