<?php
namespace Midata\Mysql\DDL;

use Midata\DDL\Column as DDLColumn;
use Midata\Object\Column as MidataColumn;
use Midata\Mysql\Column as MysqlColumn;
use Exception;

class Column extends DDLColumn
{
    private static $precision = array(
        MysqlColumn::DATA_TYPE_INT,
        MysqlColumn::DATA_TYPE_BIGINT,
        MysqlColumn::DATA_TYPE_TINYINT,
        MysqlColumn::DATA_TYPE_SMALLINT,
        MysqlColumn::DATA_TYPE_MEDIUMINT,
    );

    private static $length = array(
        MysqlColumn::DATA_TYPE_CHAR,
        MysqlColumn::DATA_TYPE_VARCHAR,
    );

    private static $precisionScale = array(
        MysqlColumn::DATA_TYPE_FLOAT,
        MysqlColumn::DATA_TYPE_DOUBLE,
        MysqlColumn::DATA_TYPE_DECIMAL,
    );

    private static $enum = array(
        MysqlColumn::DATA_TYPE_ENUM,
        MysqlColumn::DATA_TYPE_SET,
    );

    private static $empty = array(
        MysqlColumn::DATA_TYPE_TINYTEXT,
        MysqlColumn::DATA_TYPE_TEXT,
        MysqlColumn::DATA_TYPE_BLOB,
        MysqlColumn::DATA_TYPE_MEDIUMTEXT,
        MysqlColumn::DATA_TYPE_MEDIUMBLOB,
        MysqlColumn::DATA_TYPE_LONGTEXT,
        MysqlColumn::DATA_TYPE_LONGBLOB,
        MysqlColumn::DATA_TYPE_DATE,
        MysqlColumn::DATA_TYPE_DATETIME,
        MysqlColumn::DATA_TYPE_TIMESTAMP,
        MysqlColumn::DATA_TYPE_TIME,
        MysqlColumn::DATA_TYPE_YEAR,
    );

    private static $predefinedDefaultValue = array(
        'CURRENT_TIMESTAMP',
    );

    public function alter(MidataColumn $column)
    {
        $inline = $this->inline($column);
        $tableName = $column->tableName();
        $columnName = $column->name();

        $sql = "ALTER TABLE `$tableName`\nMODIFY COLUMN $inline";

        if ($column->isSupported(MidataColumn::ATTRIBUTE_AFTER)) {
            $after = $column->after();

            if (!is_null($after)) {
                $sql = "$sql AFTER $after";
            }else{
                $sql = "$sql FIRST";
            }
        }

        return "$sql;";
    }

    public function drop(MidataColumn $column)
    {
        $tableName = $column->tableName();
        $columnName = $column->name();

        $sql = "ALTER TABLE `$tableName`\nDROP COLUMN `$columnName`";

        return "$sql;";
    }

    public function create(MidataColumn $column)
    {
        $inline = $this->inline($column);
        $tableName = $column->tableName();
        $columnName = $column->name();

        $sql = "ALTER TABLE `$tableName`\nADD COLUMN $inline";

        if ($column->isSupported(MidataColumn::ATTRIBUTE_AFTER)) {
            $after = $column->after();

            if (!is_null($after)) {
                $sql = "$sql AFTER $after";
            }else{
                $sql = "$sql FIRST";
            }
        }

        return "$sql;";
    }

    public function inline(MidataColumn $column)
    {
        $assert = $this->service('assert');

        $sql = "";

        $columnName = $column->name();
        $type = $column->need(MidataColumn::ATTRIBUTE_TYPE);
        $nullable = $column->need(MidataColumn::ATTRIBUTE_NULLABLE);
        $defaultValue = $column->need(MidataColumn::ATTRIBUTE_DEFAULTVALUE);
        $length = $column->need(MidataColumn::ATTRIBUTE_LENGTH);
        $numericPrecision = $column->need(MidataColumn::ATTRIBUTE_NUMERICPRECISION);
        $numericScale = $column->need(MidataColumn::ATTRIBUTE_NUMERICSCALE);
        $unsigned = $column->need(MidataColumn::ATTRIBUTE_UNSIGNED);

        if ($unsigned) {
            $unsigned = "unsigned";
        }else{
            $unsigned = "";
        }

        $sequence = false;
        if ($column->isSupported(MidataColumn::ATTRIBUTE_SEQUENCE)) {
            $sequence = $column->sequence();
        }

        $comment = "";
        if ($column->isSupported(MidataColumn::ATTRIBUTE_COMMENT)) {
            $comment = $column->comment();
        }

        $typeOfDataType = $this->typeOfDataType($type);

        switch ($typeOfDataType) {
        case 'precision':
            $sql = "`$columnName` $type($numericPrecision) $unsigned";
            break;
        case 'precisionScale':
            $sql = "`$columnName` $tyle($numericPrecision, $numericScale) $unsigned";
            break;
        case 'length':
            $sql = "`$columnName` $type($length)";
            break;
        case 'empty':
            $sql = "`$columnName` $type";
            break;
        case 'enum':
            $enums = "";
            foreach ($column->enums() as $enum) {
                $enums .= "'$enum',";
            }

            $enums = trim($enums, ',');
            $sql = "`$columnName` $type($enums)";

            break;
        default:
            $assert->exception("Not recognized type of data type.");
            break;
        }

        if ($nullable) {
            $sql = "$sql NULL";
        }else{
            $sql = "$sql NOT NULL";
        }

        if (is_null($defaultValue)) {
            if ($column->nullable()) {
                $sql = "$sql DEFAULT NULL";
            }
        }else{
            if (in_array($defaultValue, self::$predefinedDefaultValue)) {
                $sql = "$sql DEFAULT $defaultValue";
            }else{
                $sql = "$sql DEFAULT '$defaultValue'";
            }
        }

        if (!empty($comment)) {
            $sql = "$sql COMMENT '$comment'";
        }

        if ($sequence) {
            $sql = "$sql AUTO_INCREMENT";
        }

        return $sql;
    }

    private function typeOfDataType($type)
    {
        if(in_array($type, self::$precision)){
            return "precision";
        }

        if(in_array($type, self::$precisionScale)){
            return "precisionScale";
        }

        if(in_array($type, self::$enum)){
            return "enum";
        }

        if(in_array($type, self::$empty)){
            return "empty";
        }

        if(in_array($type, self::$length)){
            return "length";
        }
    }
}
