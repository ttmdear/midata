<?php
namespace Midata\Tests\Mysql\DDL;

use Midata\Tests\Mysql;
use Midata\DDL as MidataDDL;

class ColumnTest extends Mysql
{
    public function testInt()
    {
        $adapter = $this->adapter();

        $intFull = $adapter->table('complex_table')->column('int_full');
        $ddl = $adapter->ddl(MidataDDL::COLUMN);

        $expected = "ALTER TABLE `complex_table`\nMODIFY COLUMN `int_full` int(10) unsigned NOT NULL DEFAULT '10' COMMENT 'Full column' FIRST;";
        $result = $ddl->alter($intFull);
        $this->assertEquals($result, $expected);

        $expected = "ALTER TABLE `complex_table`\nADD COLUMN `int_full` int(10) unsigned NOT NULL DEFAULT '10' COMMENT 'Full column' FIRST;";
        $result = $ddl->create($intFull);
        $this->assertEquals($result, $expected);

        $expected = "ALTER TABLE `complex_table`\nDROP COLUMN `int_full`;";
        $result = $ddl->drop($intFull);
        $this->assertEquals($result, $expected);
    }
}
