<?php
namespace Midata\Mysql;

use Midata\Object\Index as MidataIndex;

class Index extends MidataIndex
{
    const INDEX_TYPE_KEY = 'KEY';
    const INDEX_TYPE_UNIQUE = 'UNIQUE';
    const INDEX_TYPE_FULLTEXT = 'FULLTEXT';
    const INDEX_TYPE_SPATIAL = 'SPATIAL';

    const ALGORITHM_BTREE = 'BTREE';
    const ALGORITHM_HASH = 'HASH';
    const ALGORITHM_RTREE = 'RTREE';

    private $data;

    public function type()
    {
        $indexType = $this->get('indexType');
        $nonUnique = $this->get('nonUnique');

        if ($indexType == self::ALGORITHM_BTREE) {
            if ($nonUnique == '1') {
                return self::INDEX_TYPE_KEY;
            }else{
                return self::INDEX_TYPE_UNIQUE;
            }
        }elseif($indexType == self::INDEX_TYPE_FULLTEXT){
            return self::INDEX_TYPE_FULLTEXT;
        }else{
            return self::INDEX_TYPE_SPATIAL;
        }
    }

    public function algorithm()
    {
        return self::NOT_SUPPORTED;
    }

    public function columns()
    {
        return $this->get('columns');
    }

    public function get($attribute)
    {
        $assert = $this->service('assert');

        if (is_null($this->data)) {
            $schema = $this->schema();
            $indexName = $this->name();
            $tableName = $this->tableName();

            $sql = "
                SELECT
                    NON_UNIQUE,
                    COLUMN_NAME,
                    INDEX_TYPE
                FROM INFORMATION_SCHEMA.STATISTICS
                WHERE TABLE_SCHEMA = '$schema'
                and INDEX_NAME = '$indexName'
                and TABLE_NAME = '$tableName'
            ";

            $result = $this->execute($sql);

            if (empty($result)) {
                $assert->exception("There are no matadata about $indexName index.");
            }

            $columns = array();
            $indexType = null;
            $nonUnique = null;

            foreach ($result as $row) {
                $columns[] = $row['COLUMN_NAME'];
                $indexType = $row['INDEX_TYPE'];
                $nonUnique = $row['NON_UNIQUE'];
            }

            $this->data = array(
                'columns' => $columns,
                'nonUnique' => $nonUnique,
                'indexType' => $indexType,
            );
        }

        return $this->data[$attribute];
    }
}
