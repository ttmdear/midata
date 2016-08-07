<?php
namespace Midata\Object;

use Midata\Object;

abstract class Index extends Object
{
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_ALGORITHM = 'algorithm';
    const ATTRIBUTE_COLUMNS = 'columns';

    abstract public function type();
    abstract public function algorithm();
    abstract public function columns();

    public static function allAttributes()
    {
        return array(
            self::ATTRIBUTE_TYPE,
            self::ATTRIBUTE_ALGORITHM,
            self::ATTRIBUTE_COLUMNS,
        );
    }
}
