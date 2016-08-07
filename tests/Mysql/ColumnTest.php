<?php
namespace Midata\Tests\Mysql;
use Midata\Tests\Mysql;

class ColumnTest extends Mysql
{
    public function testAttributes()
    {
        $table = $this->table('complex_table');

        // `int_full` int(10) unsigned NOT NULL DEFAULT '10' COMMENT "Full column",
        $column = $table->column('int_full');
        $this->assertEquals($column->position(), '1');
        $this->assertEquals($column->defaultValue(), '10');
        $this->assertEquals($column->nullable(), false);
        $this->assertEquals($column->type(), 'int');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), '10');
        $this->assertEquals($column->numericScale(), 0);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), true);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), null);

        // `int_signed` int(10) NOT NULL DEFAULT '10' COMMENT "Full column",
        $column = $table->column('int_signed');
        $this->assertEquals($column->position(), '2');
        $this->assertEquals($column->defaultValue(), '10');
        $this->assertEquals($column->nullable(), false);
        $this->assertEquals($column->type(), 'int');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), '10');
        $this->assertEquals($column->numericScale(), 0);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'int_full');

        // `int_signed_null` int(10) DEFAULT '10' COMMENT "Full column",
        $column = $table->column('int_signed_null');
        $this->assertEquals($column->position(), '3');
        $this->assertEquals($column->defaultValue(), '10');
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'int');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), '10');
        $this->assertEquals($column->numericScale(), 0);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'int_signed');

        // `int_signed_null_nodef` int(10) COMMENT "Full column",
        $column = $table->column('int_signed_null_nodef');
        $this->assertEquals($column->position(), '4');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'int');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), '10');
        $this->assertEquals($column->numericScale(), 0);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'int_signed_null');

        // `int_clean` int(10),
        $column = $table->column('int_clean');
        $this->assertEquals($column->position(), '5');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'int');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), '10');
        $this->assertEquals($column->numericScale(), 0);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), null);
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'int_signed_null_nodef');

        // `char_full` char(10) NOT NULL DEFAULT '10' COMMENT "Full column",
        $column = $table->column('char_full');
        $this->assertEquals($column->position(), '6');
        $this->assertEquals($column->defaultValue(), '10');
        $this->assertEquals($column->nullable(), false);
        $this->assertEquals($column->type(), 'char');
        $this->assertEquals($column->length(), 10);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), 'utf8');
        $this->assertEquals($column->collation(), 'utf8_polish_ci');
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'int_clean');

        // `char_null` char(10) DEFAULT '10' COMMENT "Full column",
        $column = $table->column('char_null');
        $this->assertEquals($column->position(), '7');
        $this->assertEquals($column->defaultValue(), '10');
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'char');
        $this->assertEquals($column->length(), 10);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), 'utf8');
        $this->assertEquals($column->collation(), 'utf8_polish_ci');
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'char_full');

        // `char_null_nodef` char(10) COMMENT "Full column",
        $column = $table->column('char_null_nodef');
        $this->assertEquals($column->position(), '8');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'char');
        $this->assertEquals($column->length(), 10);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), 'utf8');
        $this->assertEquals($column->collation(), 'utf8_polish_ci');
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'char_null');

        // `char_clean` char(10),
        $column = $table->column('char_clean');
        $this->assertEquals($column->position(), '9');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'char');
        $this->assertEquals($column->length(), 10);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), 'utf8');
        $this->assertEquals($column->collation(), 'utf8_polish_ci');
        $this->assertEquals($column->comment(), null);
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'char_null_nodef');

        // `datetime_full` datetime NOT NULL DEFAULT '2015.01.01' COMMENT "Full column",
        $column = $table->column('datetime_full');
        $this->assertEquals($column->position(), '10');
        $this->assertEquals($column->defaultValue(), '2015-01-01 00:00:00');
        $this->assertEquals($column->nullable(), false);
        $this->assertEquals($column->type(), 'datetime');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), 0);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'char_clean');

        // `datetime_null` datetime DEFAULT '2015.01.01' COMMENT "Full column",
        $column = $table->column('datetime_null');
        $this->assertEquals($column->position(), '11');
        $this->assertEquals($column->defaultValue(), '2015-01-01 00:00:00');
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'datetime');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), 0);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'datetime_full');

        // `datetime_null_nodef` datetime COMMENT "Full column",
        $column = $table->column('datetime_null_nodef');
        $this->assertEquals($column->position(), '12');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'datetime');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), 0);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'datetime_null');

        // `datetime_clean` datetime,
        $column = $table->column('datetime_clean');
        $this->assertEquals($column->position(), '13');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'datetime');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), null);
        $this->assertEquals($column->numericScale(), null);
        $this->assertEquals($column->datetimePrecision(), 0);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), null);
        $this->assertEquals($column->unsigned(), null);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'datetime_null_nodef');

        // `float_full` float(10,2) unsigned NOT NULL DEFAULT '10' COMMENT "Full column",
        $column = $table->column('float_full');
        $this->assertEquals($column->position(), '14');
        $this->assertEquals($column->defaultValue(), '10.00');
        $this->assertEquals($column->nullable(), false);
        $this->assertEquals($column->type(), 'float');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), 10);
        $this->assertEquals($column->numericScale(), 2);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), true);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'datetime_clean');

        // `float_signed` float(10,2) NOT NULL DEFAULT '10' COMMENT "Full column",
        $column = $table->column('float_signed');
        $this->assertEquals($column->position(), '15');
        $this->assertEquals($column->defaultValue(), '10.00');
        $this->assertEquals($column->nullable(), false);
        $this->assertEquals($column->type(), 'float');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), 10);
        $this->assertEquals($column->numericScale(), 2);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'float_full');

        // `float_signed_null` float(10,2) DEFAULT '10' COMMENT "Full column",
        $column = $table->column('float_signed_null');
        $this->assertEquals($column->position(), '16');
        $this->assertEquals($column->defaultValue(), '10.00');
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'float');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), 10);
        $this->assertEquals($column->numericScale(), 2);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'float_signed');

        // `float_signed_null_nodef` float(10,2) COMMENT "Full column",
        $column = $table->column('float_signed_null_nodef');
        $this->assertEquals($column->position(), '17');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'float');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), 10);
        $this->assertEquals($column->numericScale(), 2);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), 'Full column');
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'float_signed_null');

        // `float_clean` float(10,2)
        $column = $table->column('float_clean');
        $this->assertEquals($column->position(), '18');
        $this->assertEquals($column->defaultValue(), null);
        $this->assertEquals($column->nullable(), true);
        $this->assertEquals($column->type(), 'float');
        $this->assertEquals($column->length(), null);
        $this->assertEquals($column->numericPrecision(), 10);
        $this->assertEquals($column->numericScale(), 2);
        $this->assertEquals($column->datetimePrecision(), null);
        $this->assertEquals($column->character(), null);
        $this->assertEquals($column->collation(), null);
        $this->assertEquals($column->comment(), null);
        $this->assertEquals($column->unsigned(), false);
        $this->assertEquals($column->sequence(), false);
        $this->assertEquals($column->select(), true);
        $this->assertEquals($column->insert(), true);
        $this->assertEquals($column->update(), true);
        $this->assertEquals($column->enums(), null);
        $this->assertEquals($column->after(), 'float_signed_null_nodef');

    }
}
