<?php
namespace Midata\Mysql\DDL;

use Midata\DDL\Index as DDLIndex;
use Midata\Object\Index as MidataIndex;
use Midata\Mysql\Index as MysqlIndex;

class Index extends DDLIndex
{
    public function create(MidataIndex $index)
    {
        $indexName = $index->name();
        $tableName = $index->tableName();
        $inline = $this->inline($index);

        $sql = "ALTER TABLE `$tableName`\nADD $inline";

        return "$sql;";
    }

    public function drop(MidataIndex $index)
    {
        $tableName = $index->tableName();
        $indexName = $index->name();

        return "DROP INDEX `$indexName` ON `$tableName`;";
    }

    public function inline(MidataIndex $index)
    {
        // CREATE TABLE `books` (
        //   `book_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        //   `name` varchar(250) COLLATE utf8_polish_ci DEFAULT 'no name',
        //   `format_id` int(10) unsigned DEFAULT NULL,
        //   `release_date` date DEFAULT NULL,
        //   PRIMARY KEY (`book_id`),
        //   UNIQUE KEY `unique_books_name` (`name`),
        //   KEY `books_format` (`format_id`),
        //   KEY `key_books_name` (`name`,`format_id`),
        //   CONSTRAINT `books_format` FOREIGN KEY (`format_id`) REFERENCES `dictionary_values` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
        // ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci

        $indexName = $index->name();
        $columns = $this->implode($index->columns());
        $type = $index->type();

        $sql = "$type `$indexName` ($columns)";

        return $sql;
    }
}
