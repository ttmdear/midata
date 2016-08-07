<?php
namespace Midata\Adapter;

use Midata\Adapter;
use PDO as PDOPhp;

use Midata\DML\Conditional\Select as MidataSelect;
use Midata\DML\Conditional\Update as MidataUpdate;
use Midata\DML\Conditional\Delete as MidataDelete;
use Midata\DML\Insert as MidataInsert;

abstract class Pdo extends Adapter
{
    private $pdo;

    /**
     * Execute query or statement.
     *
     * @param mixed $queryOrStatement
     * @return mixed The return result is depended from type of query.
     */
    public function execute($queryOrStatement)
    {
        $assert = $this->service('assert');

        if (is_string($queryOrStatement)) {
            $query = $queryOrStatement;

            $statement = $this->prepare($query);

            if ($statement->columnCount() > 0) {
                $result = $statement->fetchAll(PDOPhp::FETCH_ASSOC);
                $statement->closeCursor();
                return $result;
            }

            return true;
        }

        $statement = $queryOrStatement;

        if($statement instanceof MidataInsert){
            $sql = $statement->sql();
            $statement = $this->prepare($sql);
            return $this->pdo->lastInsertId();
        }

        if($statement instanceof MidataUpdate){
            $sql = $statement->sql();
            $statement = $this->prepare($sql);
            return true;
        }

        if($statement instanceof MidataDelete){
            $sql = $statement->sql();
            $statement = $this->prepare($sql);
            return true;
        }

        $assert->exception("I don know how to execute this statement $queryOrStatement");

        return null;
    }

    private function init()
    {
        $assert = $this->service('assert');

        if (!is_null($this->pdo)) {
            // pdo is inited
            return;
        }

        // init pdo
        $adapter = $this->config('adapter');
        $host = $this->config('host');
        $dbname = $this->config('dbname');
        $username = $this->config('username');
        $password = $this->config('password');

        $assert->hasIndex(array('mysql'), $adapter);

        // connection
        $this->pdo = new \PDO("$adapter:host=$host;dbname=$dbname", $username, $password);

        if (is_null($this->pdo)) {
            $assert->exception("The connection to source cannot be established.");
        }

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Pdo adapter add additional method prepare which return PSOStatement
     * object.
     *
     * @param string $sql
     * @return \PDOStatement
     */
    protected function prepare($sql)
    {
        $this->init();

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement;
    }

    public function fetchAll(MidataSelect $select)
    {
        $sql = $select->sql();
        $statement = $this->prepare($sql);
        $result = $statement->fetchAll(PDOPhp::FETCH_ASSOC);
        $statement->closeCursor();

        return $result;
    }

    public function fetchRow(MidataSelect $select)
    {
        $select = clone($select);
        $select->limit(1);
        $result = $this->fetchAll($select);

        if(!empty($result)){
            return $result[0];
        }else{
            return null;
        }
    }

    public function fetchCol(MidataSelect $select)
    {
        $sql = $select->sql();
        $statement = $this->prepare($sql);
        $result = $statement->fetchAll(PDOPhp::FETCH_COLUMN);
        $statement->closeCursor();

        return $result;
    }

    public function fetchOne(MidataSelect $select)
    {
        $select = clone($select);
        $select->limit(1);

        $result = $this->fetchCol($select);

        if(!empty($result)){
            return $result[0];
        }else{
            return null;
        }
    }

    public function count(MidataSelect $select)
    {
        $sql = $select->sql();

        $sql = "SELECT count(*) as count FROM ($sql) as tmp";

        $statement = $this->prepare($sql);
        $result = $statement->fetchAll(PDOPhp::FETCH_ASSOC);

        return $result[0]['count'];
    }

    public function first(MidataSelect $select)
    {
        $selectClone = clone($select);
        $selectClone->limit(1);

        $collection = $this->all($selectClone);

        return $collection->first();
    }

}
