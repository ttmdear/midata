<?php
namespace Midata\Tests\Mysql\DDL;

use Midata\Tests\Mysql;
use Midata\DDL as MidataDDL;;

class ViewTest extends Mysql
{
    public function testAlter()
    {
        $adapter = $this->adapter();

        $view = $adapter->view('authors_released_books');
        $ddl = $adapter->ddl(MidataDDL::VIEW);

        $expected = "CREATE VIEW `authors_released_books` AS\nselect `a`.`author_id` AS `author_id`,`a`.`type_id` AS `type_id`,count(`ba`.`book_id`) AS `numbers_of_books` from (`authors` `a` join `books_authors` `ba` on(((`ba`.`author_id` = `a`.`author_id`) and (`ba`.`type_id` = `a`.`type_id`)))) group by `a`.`author_id`,`a`.`type_id`;";
        $result = $ddl->create($view);
        $this->assertEquals($this->inline($result), $this->inline($expected));

        $expected = "DROP VIEW IF EXISTS `authors_released_books`;\nCREATE VIEW `authors_released_books` AS\nselect `a`.`author_id` AS `author_id`,`a`.`type_id` AS `type_id`,count(`ba`.`book_id`) AS `numbers_of_books` from (`authors` `a` join `books_authors` `ba` on(((`ba`.`author_id` = `a`.`author_id`) and (`ba`.`type_id` = `a`.`type_id`)))) group by `a`.`author_id`,`a`.`type_id`;";
        $result = $ddl->alter($view);
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
