<?php
namespace Midata\Tests\Mysql;

use Midata\Tests\Mysql;
use Midata\Mysql\Trigger as MysqlTrigger;

class TriggerTest extends Mysql
{
    public function testMetadata()
    {
        // authors_bi
        $table = $this->table('authors');
        $trigger = $table->trigger('authors_bi');
        $this->assertEquals($this->inline($trigger->statement()), $this->inline('BEGIN	set@a=10;END'));
        $this->assertEquals($trigger->event(), MysqlTrigger::EVENT_DELETE);
        $this->assertEquals($trigger->orientation(), MysqlTrigger::ORIENTATION_ROW);
        $this->assertEquals($trigger->timming(), MysqlTrigger::TIMMING_AFTER);

        // books_bi
        $table = $this->table('books');
        $trigger = $table->trigger('books_bi');
        $this->assertEquals($this->inline($trigger->statement()), $this->inline('BEGIN	IFnew.name="Jan"THEN		setnew.name="Jan1";	ENDIF;END'));
        $this->assertEquals($trigger->event(), MysqlTrigger::EVENT_UPDATE);
        $this->assertEquals($trigger->orientation(), MysqlTrigger::ORIENTATION_ROW);
        $this->assertEquals($trigger->timming(), MysqlTrigger::TIMMING_BEFORE);
    }
}
