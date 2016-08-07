<?php
namespace Midata\Mysql;

use Midata\Object\Trigger as MidataTrigger;

class Trigger extends MidataTrigger
{
    const EVENT_INSERT = 'INSERT';
    const EVENT_UPDATE = 'UPDATE';
    const EVENT_DELETE = 'DELETE';

    const TIMMING_BEFORE = 'BEFORE';
    const TIMMING_AFTER = 'AFTER';

    const ORIENTATION_ROW= 'ROW';

    private $data;

    public function event()
    {
        return $this->get('EVENT_MANIPULATION');
    }

    public function timming()
    {
        return $this->get('ACTION_TIMING');
    }

    public function statement()
    {
        $statement = $this->get("ACTION_STATEMENT");
        if ($statement === self::NOT_SUPPORTED) {
            return self::NOT_SUPPORTED;
        }

        $statement = str_replace("\t", "    ", $statement);

        return $statement;
    }

    public function orientation()
    {
        return $this->get('ACTION_ORIENTATION');
    }

    private function get($attribute)
    {
        $assert = $this->service('assert');

        if (is_null($this->data)) {
            $schema = $this->schema();
            $table = $this->tableName();
            $name = $this->name();

            $sql = "
                SELECT
                    EVENT_MANIPULATION,
                    ACTION_TIMING,
                    ACTION_STATEMENT,
                    ACTION_ORIENTATION
                FROM information_schema.TRIGGERS t
                where t.TRIGGER_SCHEMA = '$schema'
                AND t.EVENT_OBJECT_TABLE = '$table'
                AND t.TRIGGER_NAME = '$name'
            ";

            $result = $this->execute($sql);

            if (empty($result)) {
                $assert->exception("There are no metadata about $name trigger.");
            }

            $this->data = $result[0];
        }

        $data = $this->data;

        if (!in_array($attribute, array_keys($data))) {
            return self::NOT_SUPPORTED;
        }

        return $data[$attribute];

    }

}
