<?php
namespace Midata\DDL;

use Midata\DDL as MidataDDL;
use Midata\Object\View as MidataView;

abstract class View extends MidataDDL
{
    abstract public function create(MidataView $view);
    abstract public function drop(MidataView $view);
}
