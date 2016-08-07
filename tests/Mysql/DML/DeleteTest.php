<?php
namespace Midata\Tests\Mysql\DML;

use Midata\Tests\Mysql;
use Midata\DML as MidataDML;

class DeleteTest extends Mysql
{
    public function testInit()
    {
        $adapter = $this->adapter('bookstore');
        $dml = $adapter->dml(MidataDML::DELETE);

        $dml->table('books');
        $dml->equal('book_id', 10)
            ->like('title', "Javascript")
            ->in('book_id', array())
            ->in('book_id', array(1,2))
            ->brackets(function($dml){
                $dml->orOperator();
                $dml->startWith('title', 'A');
                $dml->endWith('title', 'b');
            })
        ;

        $expected = "DELETE FROM `books` WHERE book_id = 10 AND title like 'Javascript' AND 1=2 AND book_id in(1,2) AND (title like 'A%' OR title like '%b')";
        $result = $dml->sql();
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
