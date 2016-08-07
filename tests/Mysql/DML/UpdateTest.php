<?php
namespace Midata\Tests\Mysql\DML;

use Midata\Tests\Mysql;
use Midata\DML as MidataDML;

class UpdateTest extends Mysql
{
    public function testInit()
    {
        $adapter = $this->adapter('bookstore');
        $dml = $adapter->dml(MidataDML::UPDATE);

        $dml->table('books')
            ->values(array(
                'book_id' => '20',
                'title' => 'Javascript programming.'
            ))
            ->in('book_id', array())
            ->equal('book_id', 1)
            ->brackets(function($select){
                $select->startWith('title', 'a');

                $select->brackets(function($select){
                    $select->orOperator();
                    $select->startWith('title', 'G');
                    $select->endWith('title', 'a');
                });
            })
        ;

        $expected = "UPDATE `books` SET `book_id` = '20',`title` = 'Javascript programming.' WHERE 1=2 AND book_id = 1 AND (title like 'a%' AND (title like 'G%' OR title like '%a'))";
        $result = $dml->sql();
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }

}
