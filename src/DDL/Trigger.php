<?php
namespace Midata\DDL;

use Midata\DDL as MidataDDL;
use Midata\Object\Trigger as MidataTrigger;

abstract class Trigger extends MidataDDL
{
    abstract public function create(MidataTrigger $trigger);
    abstract public function drop(MidataTrigger $trigger);
}
