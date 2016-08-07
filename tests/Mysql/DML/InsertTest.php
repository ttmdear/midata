<?php
namespace Midata\Tests\Mysql\DML;

use Midata\Tests\Mysql;
use Midata\DML as MidataDML;

class InsertTest extends Mysql
{
    public function testInit()
    {
        $adapter = $this->adapter('bookstore');
        $dml = $adapter->dml(MidataDML::INSERT);

        $dml->table('books')
            ->values(array(
                'book_id' => '20',
                'title' => 'Javascript programming.'
            ))
        ;

        $expected = "INSERT INTO `books` (`book_id`,`title`) VALUES ('20','Javascript programming.')";
        $result = $dml->sql();
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
