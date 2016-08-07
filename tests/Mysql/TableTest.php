<?php
namespace Midata\Tests\Mysql;
use Midata\Tests\Mysql;

class TableTest extends Mysql
{
    public function testColumns()
    {
        $table = $this->table('authors');
        $expected = "array(0=>'author_id',1=>'type_id',2=>'last_name',3=>'first_name',4=>'birth_date',)";
        $result = $table->columns();
        $this->assertEquals($this->inline($result), $expected);
    }

    public function testIndexes()
    {
        $table = $this->table('authors');
        $expected = "array()";
        $result = $table->indexes();
        $this->assertEquals($this->inline($result), $expected);

        $table = $this->table('books');
        $expected = "array(0=>'unique_books_name',1=>'key_books_name',)";
        $result = $table->indexes();
        $this->assertEquals($this->inline($result), $expected);
    }

    public function testPrimaryKey()
    {
        $table = $this->table('books_authors');
        $expected = "array(0=>'book_id',1=>'author_id',2=>'type_id',)";
        $result = $table->primaryKey();
        $this->assertEquals($this->inline($result), $expected);

        $table = $this->table('books');
        $expected = "array(0=>'book_id',)";
        $result = $table->primaryKey();
        $this->assertEquals($this->inline($result), $expected);

        $table = $this->table('no_key');
        $expected = "array()";
        $result = $table->primaryKey();
        $this->assertEquals($this->inline($result), $expected);
    }

    public function testTriggers()
    {
        $table = $this->table('authors');
        $expected = "array(0=>'authors_bi',)";
        $result = $table->triggers();
        $this->assertEquals($this->inline($result), $expected);

        $table = $this->table('books');
        $expected = "array(0=>'books_bi',)";
        $result = $table->triggers();
        $this->assertEquals($this->inline($result), $expected);
    }
}
