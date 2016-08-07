<?php
namespace Midata\Adapter\Pdo;

use Midata\Adapter\Pdo;

class Mysql extends Pdo
{
    public function tables()
    {
        $dbname = $this->config('dbname');

        $sql = "
            SELECT
                v.TABLE_NAME
            FROM information_schema.TABLES v
            where v.TABLE_SCHEMA = '$dbname'
            AND v.TABLE_TYPE = 'BASE TABLE'
        ";

        $result = $this->execute($sql);

        if (empty($result)) {
            return array();
        }

        foreach ($result as $row) {
            $tables[] = $row['TABLE_NAME'];
        }

        return $tables;
    }

    public function views()
    {
        $dbname = $this->config('dbname');

        $sql = "
            SELECT
                TABLE_NAME
            FROM information_schema.VIEWS v
            where v.TABLE_SCHEMA = '$dbname'
        ";

        $result = $this->execute($sql);

        $views = array();

        foreach ($result as $row) {
            $views[] = $row['TABLE_NAME'];
        }

        return $views;
    }
}
