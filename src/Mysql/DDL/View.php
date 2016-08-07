<?php
namespace Midata\Mysql\DDL;

use Midata\DDL\View as DDLView;
use Midata\Object\View as MidataView;

class View extends DDLView
{
    public function create(MidataView $view)
    {
        $definition = $view->definition();
        $name = $view->name();

        $sql = "CREATE VIEW `$name` AS\n$definition";

        return "$sql;";
    }

    public function drop(MidataView $view)
    {
        $name = $view->name();
        return "DROP VIEW IF EXISTS `$name`;";
    }
}
