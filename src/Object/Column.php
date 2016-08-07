<?php
namespace Midata\Object;

use Midata\Object;

abstract class Column extends Object
{
    const ATTRIBUTE_POSITION = 'position';
    const ATTRIBUTE_DEFAULTVALUE = 'defaultValue';
    const ATTRIBUTE_NULLABLE = 'nullable';
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_LENGTH = 'length';
    const ATTRIBUTE_NUMERICPRECISION = 'numericPrecision';
    const ATTRIBUTE_NUMERICSCALE = 'numericScale';
    const ATTRIBUTE_DATETIMEPRECISION = 'datetimePrecision';
    const ATTRIBUTE_CHARACTER = 'character';
    const ATTRIBUTE_COLLATION = 'collation';
    const ATTRIBUTE_COMMENT = 'comment';
    const ATTRIBUTE_UNSIGNED = 'unsigned';
    const ATTRIBUTE_SEQUENCE = 'sequence';
    const ATTRIBUTE_SELECT = 'select';
    const ATTRIBUTE_INSERT = 'insert';
    const ATTRIBUTE_UPDATE = 'update';
    const ATTRIBUTE_ENUMS = 'enums';
    const ATTRIBUTE_AFTER = 'after';

    abstract public function position($value = null);
    abstract public function defaultValue($value = null);
    abstract public function nullable($value = null);
    abstract public function type($value = null);
    abstract public function length($value = null);
    abstract public function numericPrecision($value = null);
    abstract public function numericScale($value = null);
    abstract public function datetimePrecision($value = null);
    abstract public function character($value = null);
    abstract public function collation($value = null);
    abstract public function comment($value = null);
    abstract public function unsigned($value = null);
    abstract public function sequence($value = null);
    abstract public function select($value = null);
    abstract public function insert($value = null);
    abstract public function update($value = null);
    abstract public function enums($value = null);
    abstract public function after($value = null);

    public static function allAttributes()
    {
        return array(
            self::ATTRIBUTE_POSITION,
            self::ATTRIBUTE_DEFAULTVALUE,
            self::ATTRIBUTE_NULLABLE,
            self::ATTRIBUTE_TYPE,
            self::ATTRIBUTE_LENGTH,
            self::ATTRIBUTE_NUMERICPRECISION,
            self::ATTRIBUTE_NUMERICSCALE,
            self::ATTRIBUTE_DATETIMEPRECISION,
            self::ATTRIBUTE_CHARACTER,
            self::ATTRIBUTE_COLLATION,
            self::ATTRIBUTE_COMMENT,
            self::ATTRIBUTE_UNSIGNED,
            self::ATTRIBUTE_SEQUENCE,
            self::ATTRIBUTE_SELECT,
            self::ATTRIBUTE_INSERT,
            self::ATTRIBUTE_UPDATE,
            self::ATTRIBUTE_ENUMS,
            self::ATTRIBUTE_AFTER,
        );
    }
}
