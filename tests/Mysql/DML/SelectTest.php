<?php
namespace Midata\Tests\Mysql\DML;

use Midata\Tests\Mysql;
use Midata\DML as MidataDML;

class SelectTest extends Mysql
{
    public function testInit()
    {
        $adapter = $this->adapter('bookstore');
        $dml = $adapter->dml(MidataDML::SELECT);

        $dml->from('books')
            ->in('book_id', array())
            ->equal('book_id', 1)
            ->column('title')
            ->brackets(function($select){
                $select->startWith('title', 'a');

                $select->brackets(function($select){
                    $select->orOperator();
                    $select->startWith('title', 'G');
                    $select->endWith('title', 'a');
                });
            })
        ;

        $expected = "SELECT (books.title) as title FROM `books` WHERE 1=2 AND books.book_id = 1 AND (books.title like 'a%' AND (books.title like 'G%' OR books.title like '%a'))";
        $result = $dml->sql();
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }

}
