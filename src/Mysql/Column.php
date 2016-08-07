<?php
namespace Midata\Mysql;

use Midata\Object\Column as MidataColumn;

class Column extends MidataColumn
{
    // precision
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_BIGINT = 'bigint';
    const DATA_TYPE_TINYINT = 'tinyint';
    const DATA_TYPE_SMALLINT = 'smallint';
    const DATA_TYPE_MEDIUMINT = 'mediumint';

    // length
    const DATA_TYPE_CHAR = 'char';
    const DATA_TYPE_VARCHAR = 'varchar';

    // precision and scale
    const DATA_TYPE_FLOAT = 'float';
    const DATA_TYPE_DOUBLE = 'double';
    const DATA_TYPE_DECIMAL = 'decimal';

    // enum
    const DATA_TYPE_ENUM = 'enum';
    const DATA_TYPE_SET = 'set';

    // empty
    const DATA_TYPE_TINYTEXT = 'tinytext';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_BLOB = 'blob';
    const DATA_TYPE_MEDIUMTEXT = 'mediumtext';
    const DATA_TYPE_MEDIUMBLOB = 'mediumblob';
    const DATA_TYPE_LONGTEXT = 'longtext';
    const DATA_TYPE_LONGBLOB = 'longblob';
    const DATA_TYPE_DATE = 'date';
    const DATA_TYPE_DATETIME = 'datetime';
    const DATA_TYPE_TIMESTAMP = 'timestamp';
    const DATA_TYPE_TIME = 'time';
    const DATA_TYPE_YEAR = 'year';

    private $info;

    public function position($value = self::GETTER)
    {
        $this->overwrite('position', $value);
        if($this->isOverwrite('position')){
            return $this->getOverwrite('position');
        }

        return $this->get('ORDINAL_POSITION');
    }

    public function defaultValue($value = self::GETTER)
    {
        $this->overwrite('defaultValue', $value);
        if($this->isOverwrite('defaultValue')){
            return $this->getOverwrite('defaultValue');
        }

        return $this->get('COLUMN_DEFAULT');
    }

    public function nullable($value = self::GETTER)
    {
        $this->overwrite('nullable', $value);
        if($this->isOverwrite('nullable')){
            return $this->getOverwrite('nullable');
        }

        if($this->get('IS_NULLABLE') == 'YES'){
            return true;
        }else{
            return false;
        }
    }

    public function type($value = self::GETTER)
    {
        $this->overwrite('type', $value);
        if($this->isOverwrite('type')){
            return $this->getOverwrite('type');
        }

        return $this->get('DATA_TYPE');
    }

    public function enums($value = self::GETTER)
    {
        $this->overwrite('enums', $value);
        if($this->isOverwrite('enums')){
            return $this->getOverwrite('enums');
        }

        $assert = $this->service('assert');

        if ($this->type() != self::DATA_TYPE_ENUM) {
            return null;
        }

        $attribute = $this->get('COLUMN_TYPE');

        if($attribute == self::NOT_SUPPORTED){
            return self::NOT_SPORTED;
        }

        $re = "/\\((.*)\\)/";
        preg_match($re, $attribute, $matches);

        if (empty($matches)) {
            $assert->exception("There are wrong type of enum");
        }

        $enums = explode(',', str_replace("'", "", $matches[1]));

        return $enums;
    }

    public function length($value = self::GETTER)
    {
        $this->overwrite('length', $value);
        if($this->isOverwrite('length')){
            return $this->getOverwrite('length');
        }

        return $this->get('CHARACTER_MAXIMUM_LENGTH');
    }

    public function numericPrecision($value = self::GETTER)
    {
        $this->overwrite('numericPrecision', $value);
        if($this->isOverwrite('numericPrecision')){
            return $this->getOverwrite('numericPrecision');
        }

        return $this->get('NUMERIC_PRECISION');
    }

    public function numericScale($value = self::GETTER)
    {
        $this->overwrite('numericScale', $value);
        if($this->isOverwrite('numericScale')){
            return $this->getOverwrite('numericScale');
        }

        return $this->get('NUMERIC_SCALE');
    }

    public function datetimePrecision($value = self::GETTER)
    {
        $this->overwrite('datetimePrecision', $value);
        if($this->isOverwrite('datetimePrecision')){
            return $this->getOverwrite('datetimePrecision');
        }

        return $this->get('DATETIME_PRECISION');
    }

    public function character($value = self::GETTER)
    {
        $this->overwrite('character', $value);
        if($this->isOverwrite('character')){
            return $this->getOverwrite('character');
        }

        return $this->get('CHARACTER_SET_NAME');
    }

    public function collation($value = self::GETTER)
    {
        $this->overwrite('collation', $value);
        if($this->isOverwrite('collation')){
            return $this->getOverwrite('collation');
        }

        return $this->get('COLLATION_NAME');
    }

    public function comment($value = self::GETTER)
    {
        $this->overwrite('comment', $value);
        if($this->isOverwrite('comment')){
            return $this->getOverwrite('comment');
        }

        return $this->get('COLUMN_COMMENT');
    }

    public function unsigned($value = self::GETTER)
    {
        $this->overwrite('unsigned', $value);
        if($this->isOverwrite('unsigned')){
            return $this->getOverwrite('unsigned');
        }

        $attribute = $this->get('COLUMN_TYPE');

        if($attribute == self::NOT_SUPPORTED){
            return self::NOT_SPORTED;
        }

        if (strpos($attribute, 'unsigned') !== false) {
            return true;
        }

        return false;
    }

    public function sequence($value = self::GETTER)
    {
        $this->overwrite('sequence', $value);
        if($this->isOverwrite('sequence')){
            return $this->getOverwrite('sequence');
        }

        $attribute = $this->get('EXTRA');

        if($attribute == self::NOT_SUPPORTED){
            return self::NOT_SPORTED;
        }

        if (strpos($attribute, 'auto_increment') !== false) {
            return true;
        }

        return false;
    }

    public function select($value = self::GETTER)
    {
        $this->overwrite('select', $value);
        if($this->isOverwrite('select')){
            return $this->getOverwrite('select');
        }

        $attribute = $this->get('PRIVILEGES');

        if($attribute == self::NOT_SUPPORTED){
            return self::NOT_SPORTED;
        }

        if (strpos($attribute, 'select') !== false) {
            return true;
        }

        return false;
    }

    public function insert($value = self::GETTER)
    {
        $this->overwrite('insert', $value);
        if($this->isOverwrite('insert')){
            return $this->getOverwrite('insert');
        }

        $attribute = $this->get('PRIVILEGES');

        if($attribute == self::NOT_SUPPORTED){
            return self::NOT_SPORTED;
        }

        if (strpos($attribute, 'insert') !== false) {
            return true;
        }

        return false;
    }

    public function update($value = self::GETTER)
    {
        $this->overwrite('update', $value);
        if($this->isOverwrite('update')){
            return $this->getOverwrite('update');
        }

        $attribute = $this->get('PRIVILEGES');

        if($attribute == self::NOT_SUPPORTED){
            return self::NOT_SPORTED;
        }

        if (strpos($attribute, 'update') !== false) {
            return true;
        }

        return false;
    }

    public function after($value = self::GETTER)
    {
        $this->overwrite('after', $value);
        if($this->isOverwrite('after')){
            return $this->getOverwrite('after');
        }

        $assert = $this->service('assert');

        $schema = $this->schema();
        $tableName = $this->tableName();
        $column = $this->name();

        $info = $this->adapter()->execute("
            SELECT
                COLUMN_NAME,
                ORDINAL_POSITION
            FROM INFORMATION_SCHEMA.COLUMNS info
            where info.TABLE_SCHEMA = '$schema'
            and table_name = '$tableName'
            ORDER BY ORDINAL_POSITION
        ");

        if (empty($info)) {
            $assert->exception("There are no metadata about column $column at $tableName");
        }

        $after = null;
        foreach ($info as $row) {
            if($row['COLUMN_NAME'] == $column){
                break;
            }

            $after = $row['COLUMN_NAME'];
        }

        return $after;
    }

    private function get($attribute)
    {
        $assert = $this->service('assert');

        if (is_null($this->info)) {
            $schema = $this->schema();
            $tableName = $this->tableName();
            $column = $this->name();

            $info = $this->adapter()->execute("
                SELECT
                    *
                FROM INFORMATION_SCHEMA.COLUMNS info
                where info.TABLE_SCHEMA = '$schema'
                and table_name = '$tableName'
                AND COLUMN_NAME = '$column';
            ");

            if (empty($info)) {
                $assert->exception("There are no metadata about column $column at $tableName");
            }

            $this->info = $info[0];
        }

        $info = $this->info;

        if (!in_array($attribute, array_keys($info))) {
            return self::NOT_SUPPORTED;
        }

        return $info[$attribute];
    }
}
