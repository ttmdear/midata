<?php
namespace Midata\DDL;

use Midata\DDL as MidataDDL;
use Midata\Object\Index as MidataIndex;

abstract class Index extends MidataDDL
{
    abstract public function create(MidataIndex $index);
    abstract public function drop(MidataIndex $index);
    abstract public function inline(MidataIndex $index);
}
