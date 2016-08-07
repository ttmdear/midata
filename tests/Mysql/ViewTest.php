<?php
namespace Midata\Tests\Mysql;

use Midata\Tests\Mysql;

class ViewTest extends Mysql
{
    public function testMetadata()
    {
        $view = $this->view('authors_books');

        $expected = "select`a`.`author_id`AS`author_id`,`a`.`type_id`AS`type_id`,`b`.`name`AS`name`from((`authors``a`join`books_authors``ba`on(((`ba`.`author_id`=`a`.`author_id`)and(`ba`.`type_id`=`a`.`type_id`))))join`books``b`on((`b`.`book_id`=`ba`.`book_id`)))";
        $result = $view->definition();
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
