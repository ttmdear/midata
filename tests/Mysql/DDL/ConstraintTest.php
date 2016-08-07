<?php
namespace Midata\Tests\Mysql\DDL;

use Midata\Tests\Mysql;

use Midata\DDL as MidataDDL;;

class ConstraintTest extends Mysql
{
    public function testPrimaryKey()
    {
        $adapter = $this->adapter();
        $ddl = $adapter->ddl(MidataDDL::CONSTRAINT);

        $table = $adapter->table('books_authors');
        $constraint = $table->constraint("PRIMARY");

        $expected = "ALTER TABLE `books_authors`\nADD PRIMARY KEY (`book_id`,`author_id`,`type_id`);";
        $result = $ddl->create($constraint);
        $this->assertEquals($this->inline($result), $this->inline($expected));

        $expected = "ALTER TABLE `books_authors` DROP PRIMARY KEY;";
        $result = $ddl->drop($constraint);
        $this->assertEquals($this->inline($result), $this->inline($expected));

        // authors
        $table = $adapter->table('books');
        $constraint = $table->constraint("PRIMARY");

        $expected = "ALTER TABLE `books`\n MODIFY COLUMN `book_id` int(10) unsigned NOT NULL FIRST;\n ALTER TABLE `books` DROP PRIMARY KEY;";
        $result = $ddl->drop($constraint);
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }

    public function testForeignKey()
    {
        $adapter = $this->adapter();
        $ddl = $adapter->ddl(MidataDDL::CONSTRAINT);

        $table = $adapter->table('books_authors');
        $constraint = $table->constraint("books_authors_author");

        $expected = "ALTER TABLE `books_authors`\nADD CONSTRAINT `books_authors_author` FOREIGN KEY (`author_id`,`type_id`) REFERENCES `authors` (`author_id`,`type_id`);";
        $result = $ddl->create($constraint);
        $this->assertEquals($this->inline($result), $this->inline($expected));

        $expected = "DROP INDEX `books_authors_author`\nON `books_authors`;\nALTER TABLE `books_authors`\nDROP FOREIGN KEY `books_authors_author`;";
        $result = $ddl->drop($constraint);
        $this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
