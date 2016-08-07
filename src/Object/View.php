<?php
namespace Midata\Object;

use Midata\Object;

/**
 * This is class represents the database view.
 */
abstract class View extends Object
{
    const ATTRIBUTE_DEFINITION = 'definition';

    abstract public function definition();

    public static function allAttributes()
    {
        return array(
            self::ATTRIBUTE_DEFINITION
        );
    }
}
