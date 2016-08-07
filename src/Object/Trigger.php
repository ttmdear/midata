<?php
namespace Midata\Object;

use Midata\Object;

abstract class Trigger extends Object
{
    const ATTRIBUTE_EVENT = 'event';
    const ATTRIBUTE_TIMMING = 'timming';
    const ATTRIBUTE_STATEMENT = 'statement';
    const ATTRIBUTE_ORIENTATION = 'orientation';

    abstract public function event();
    abstract public function timming();
    abstract public function statement();
    abstract public function orientation();

    public static function allAttributes()
    {
        return array(
            self::ATTRIBUTE_EVENT,
            self::ATTRIBUTE_TIMMING,
            self::ATTRIBUTE_STATEMENT,
            self::ATTRIBUTE_ORIENTATION,
        );
    }
}
