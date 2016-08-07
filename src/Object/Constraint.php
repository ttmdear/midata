<?php
namespace Midata\Object;

use Midata\Object;

abstract class Constraint extends Object
{
    const ATTRIBUTE_BASETABLE = 'baseTable';
    const ATTRIBUTE_REFTABLE = 'refTable';
    const ATTRIBUTE_ONDELETE = 'onDelete';
    const ATTRIBUTE_ONUPDATE = 'onUpdate';
    const ATTRIBUTE_COLUMNS = 'columns';

    abstract public function isPrimaryKey();
    abstract public function isForeignKey();

    public static function allAttributes()
    {
        return array(
            self::ATTRIBUTE_BASETABLE,
            self::ATTRIBUTE_REFTABLE,
            self::ATTRIBUTE_ONDELETE,
            self::ATTRIBUTE_ONUPDATE,
            self::ATTRIBUTE_COLUMNS,
        );
    }

}
