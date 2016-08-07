<?php
namespace Midata\Tests\Mysql\DDL;

use Midata\Tests\Mysql;
use Midata\DDL as MidataDDL;

class TriggerTest extends Mysql
{
    public function testAlter()
    {
        $adapter = $this->adapter();

        $table = $adapter->table('authors');
        $ddl = $adapter->ddl(MidataDDL::TRIGGER);
        $trigger = $table->trigger('authors_bi');

        $expected = "DROP TRIGGER `authors_bi`;\n DELIMITER $$\n CREATE TRIGGER `authors_bi`\n AFTER DELETE on `authors`\n FOR EACH ROW\n BEGIN\n set @a = 10;\n END\n $$\n DELIMITER ;";
        $result = $ddl->alter($trigger);
        //@todo : dopisac test na triggera
        //$this->assertEquals($this->inline($result), $this->inline($expected));
    }
}
