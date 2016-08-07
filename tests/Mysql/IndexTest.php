<?php
namespace Midata\Tests\Mysql;
use Midata\Tests\Mysql;

use Midata\Mysql\Index as MysqlIndex;

class IndexTest extends Mysql
{
    public function testAttributes()
    {
        $table = $this->table('books');

        // unique_books_name
        $index = $table->index('unique_books_name');
        $this->assertEquals($index->type(), MysqlIndex::INDEX_TYPE_UNIQUE);

        $expected = "array(0=>'name',)";
        $result = $index->columns();
        $this->assertEquals($this->inline($result), $expected);

        // key_books_name
        $index = $table->index('key_books_name');
        $this->assertEquals($index->type(), MysqlIndex::INDEX_TYPE_KEY);

        $expected = "array(0=>'name',1=>'format_id',)";
        $result = $index->columns();
        $this->assertEquals($this->inline($result), $expected);
    }
}
