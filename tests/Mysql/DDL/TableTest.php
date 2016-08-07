<?php
namespace Midata\Tests\Mysql\DDL;

use Midata\Tests\Mysql;
use Midata\DDL as MidataDDL;

class TableTest extends Mysql
{
    public function testAlter()
    {
        $adapter = $this->adapter();

        $table = $adapter->table('books');
        $ddl = $adapter->ddl(MidataDDL::TABLE);

        $expected = "DROP TABLE IF EXISTS `books`;\n CREATE TABLE `books`(\n `book_id` int(10) unsigned NOT NULL AUTO_INCREMENT,\n `name` varchar(250) NULL DEFAULT 'no name',\n `format_id` int(10) unsigned NULL DEFAULT NULL,\n `release_date` date NULL DEFAULT NULL,\n PRIMARY KEY (`book_id`),\n CONSTRAINT `books_format` FOREIGN KEY (`format_id`) REFERENCES `dictionary_values` (`id`) ON UPDATE SET NULL ON DELETE SET NULL,\n UNIQUE `unique_books_name` (`name`),\n KEY `key_books_name` (`name`,`format_id`)\n);";
        $result = $ddl->alter($table);
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
