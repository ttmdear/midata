<?php
namespace Midata\DDL;

use Midata\DDL as MidataDDL;
use Midata\Object\Constraint as MidataConstraint;

abstract class Constraint extends MidataDDL
{
    abstract public function create(MidataConstraint $constraint);
    abstract public function drop(MidataConstraint $constraint);
    abstract public function inline(MidataConstraint $constraint);
}
