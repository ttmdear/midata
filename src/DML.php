<?php
namespace Midata;

use Midata\Midata;
use Midata\DML\Conditional\Select as MidataSelect;

// mysql
use Midata\Adapter\Pdo\Mysql as MysqlAdapter;
use Midata\Mysql\DML\Select as BuilderMysqlSelect;
use Midata\Mysql\DML\Update as BuilderMysqlUpdate;
use Midata\Mysql\DML\Delete as BuilderMysqlDelete;
use Midata\Mysql\DML\Insert as BuilderMysqlInsert;

abstract class DML extends Midata
{
    const INSERT = 'insert';
    const DELETE = 'delete';
    const UPDATE = 'update';
    const SELECT = 'select';

    private $uniqueId = 1;
    private $binds = array();

    /**
     * Each object is related with adapter.
     *
     * @var \Midata\Adapter $adapter Reference to adapter of object.
     */
    private $adapter;

    private static $mapOfClass = array(
        MysqlAdapter::class => array(
            self::INSERT => BuilderMysqlInsert::class,
            self::DELETE => BuilderMysqlDelete::class,
            self::UPDATE => BuilderMysqlUpdate::class,
            self::SELECT => BuilderMysqlSelect::class,
        )
    );

    public static function factory($adapter, $statement)
    {
        $assert = static::service('assert');
        $class = get_class($adapter);

        if (!isset(self::$mapOfClass[$class])) {
            $assert->exception("There are no supported of DML for $class adapter.");
        }

        $classes = self::$mapOfClass[$class];

        if (!isset($classes[$statement])) {
            $assert->exception("There are no supported of DML $statement for $class adapter.");
        }

        $ddlBuilder = $classes[$statement];
        $ddlBuilder = new $ddlBuilder();
        $ddlBuilder->adapter($adapter);

        return $ddlBuilder;
    }

    protected function uniqueId()
    {
        return "a".$this->uniqueId++;
    }

    public function bind($bind, $value)
    {
        $this->binds[$bind] = $value;
        return $this;
    }

    public function binds($binds = null)
    {
        if (is_null($binds)) {
            return $this->binds;
        }

        $assert = $this->service('assert');
        $assert->isArray($binds);

        foreach ($binds as $bind => $value) {
            $this->bind($bind, $value);
        }

        return $this;
    }

    /**
     * Return sql readu to execute.
     *
     * @return string
     */
    public function sql($source = null)
    {
        return $this->adapter($source)->sql($this);
    }

    /**
     * Execute statement by source adapter.
     *
     * @param string $source Name of source.
     * @return mixed The return value is depended from adapter.
     */
    public function execute($source = null)
    {
        return $this->adapter($source)->execute($this);
    }

    /**
     * Add quote to value.
     *
     * @param string $value
     * @return string
     */
    protected function quote($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }

    protected function processSql($sql)
    {
        $assert = $this->service('assert');
        $sql = $this->processTemplate($sql);

        $reg = "/:(.*?):/";
        preg_match_all($reg, $sql, $matches);

        $binds = $this->binds();

        if (!empty($matches)) {
            $count = count($matches[0]);
            for ($i=0; $i < $count; $i++) {
                $bind = $matches[1][$i];

                $assert->hasIndex($binds, $bind, "There is not bind $bind");
                $value = $binds[$bind];

                if($value instanceof MidataSelect) {
                    // bind is select statement
                    $sql = str_replace(":$bind:", "(".$value->sql().")", $sql);
                }elseif(is_string($value) || is_int($value) || is_float($value)){
                    $sql = str_replace(":$bind:", $this->quote($value), $sql);
                }else{
                    $assert->exception("I can not proper parse bind $bind.");
                }
            }
        }

        return $sql;
    }

    protected function processTemplate($sql)
    {
        $assert = $this->service('assert');

        $reg = "/\\{(in) (.*?) (.*?)\\}/";
        preg_match_all($reg, $sql, $matches);

        $binds = $this->binds();

        foreach($matches[0] as $index => $match){
            $function = $matches[1][$index];

            switch($function){

            case 'in':
                $column = $matches[2][$index];
                $bind = $matches[3][$index];

                $assert->hasIndex($binds, $bind, "There is not bind $bind");
                $value = $binds[$bind];

                if(empty($value)){
                    $sql = str_replace("$match", "1=2", $sql);
                }elseif ($value instanceof MidataSelect) {
                    $sql = str_replace("$match", "$column in(".$value->sql().")", $sql);
                }else{
                    if (!is_array($value)) {
                        $value = array($value);
                    }

                    $in = "";
                    foreach($value as $value){
                        $in .= $this->quote($value).",";
                    }

                    $in = rtrim($in, ',');
                    $sql = str_replace("$match", "$column in($in)", $sql);
                }

                break;
            }
        }

        return $sql;
    }
    /**
     * Sets or returns the database adapter.
     *
     * @param \Midata\Adapter $adapter
     * @return \Midata\Adapter|self
     */
    public function adapter(Adapter $adapter = null)
    {
        if (is_null($adapter)) {
            if (is_null($this->adapter)) {
                if (!is_null($this->table)) {
                    return $this->table->adapter();
                }else{
                    return $this->adapter;
                }
            }else{
                return $this->adapter;
            }
        }

        $this->adapter = $adapter;

        return $this;
    }
}
