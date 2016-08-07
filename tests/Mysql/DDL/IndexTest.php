<?php
namespace Midata\Tests\Mysql\DDL;

use Midata\Tests\Mysql;

use Midata\DDL as MidataDDL;;

class IndexTest extends Mysql
{
    public function testAlter()
    {
        $adapter = $this->adapter();

        $table = $adapter->table('books');
        $ddl = $adapter->ddl(MidataDDL::INDEX);
        $index = $table->index('unique_books_name');

        $expected = "DROP INDEX `unique_books_name` ON `books`;\nALTER TABLE `books`\nADD UNIQUE `unique_books_name` (`name`);";
        $result = $ddl->alter($index);
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
