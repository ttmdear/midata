<?php
namespace Midata\DML\Conditional;

use Midata\DML\Conditional as DMLConditional;

class Select extends DMLConditional
{
    CONST JOIN_TYPE_INNER = 'inner';
    CONST JOIN_TYPE_LEFT = 'left';
    CONST JOIN_TYPE_RIGHT = 'right';
    CONST JOIN_TYPE_OUTER = 'outer';

    CONST COLUMN_TABLE = 'table';
    CONST COLUMN_EXPR = 'expr';

    CONST ORDER_ASC = 'ASC';
    CONST ORDER_DESC = 'DESC';

    private $columns = array();
    private $from = null;
    private $limit = null;
    private $orders = array();
    private $joins = array();
    private $lastJoinedTable = array();
    private $aliasIterator = 1;

    // Iterator
    private $collection;
    private $position = 0;

    public function prepare(){}

    // todo : dorobic obsluge kolumn
    public function from($table = null)
    {
        if (is_null($table)) {
            return $this->from;
        }

        $assert = $this->service('assert');

        if (is_string($table)) {
            // zostala podana tabela wiec od razu generujemy alias taki jak
            // nazwa tabeli
            $table = array($table => $table);
        }

        $assert->isArray($table, "From should be array or string.");
        $this->from = $table;

        return $this;
    }

    private function newAlias()
    {
        $iterator = $this->aliasIterator++;
        return "alias$iterator";
    }

    /**
     * Return alias of from in select statement.
     *
     * @return string
     */
    public function alias()
    {
        $assert = $this->service('assert');

        if (is_null($this->from)) {
            $assert->exception("use setFrom before other operations.");
        }

        return array_keys($this->from)[0];
    }

    /**
     * Return the table from "FROM" of select.
     *
     * @return string
     */
    public function table()
    {
        $assert = $this->service('assert');

        $from = $this->from();

        $assert->isArray($from);

        return $from[$this->alias()];
    }

    // where
    public function equal($column, $to)
    {
        $column = $this->addAliasToColumn($column);
        return parent::equal($column, $to);
    }

    public function in($column, $in)
    {
        $column = $this->addAliasToColumn($column);
        return parent::in($column, $in);
    }

    public function like($column, $like)
    {
        $column = $this->addAliasToColumn($column);
        return parent::like($column, $like);
    }

    public function startWith($column, $like)
    {
        $column = $this->addAliasToColumn($column);
        return parent::like($column, "$like%");
    }

    public function endWith($column, $like)
    {
        $column = $this->addAliasToColumn($column);
        return parent::like($column, "%$like");
    }

    public function contains($column, $like)
    {
        $column = $this->addAliasToColumn($column);
        return parent::like($column, "%$like%");
    }

    private function addAliasToColumn($column)
    {
        $column = strpos($column, '.') !== false ? $column : $this->alias().'.'.$column;
        return $column;
    }

    // joins
    public function innerJoin($table, $condition, $columns = array())
    {
        return $this->join($table, $condition, $columns, self::JOIN_TYPE_INNER);
    }

    public function leftJoin($table, $condition, $columns = array())
    {
        return $this->join($table, $condition, $columns, self::JOIN_TYPE_LEFT);
    }

    public function rightJoin($table, $condition, $columns = array())
    {
        return $this->join($table, $condition, $columns, self::JOIN_TYPE_RIGHT);
    }

    public function outerJoin($table, $condition, $columns = array())
    {
        return $this->join($table, $condition, $columns, self::JOIN_TYPE_OUTER);
    }

    private function join($table, $condition = null, $columns = array(), $type = self::JOIN_TYPE_INNER)
    {
        $assert = $this->service('assert');

        $alias = $table;

        if (is_array($table)) {
            // alias is given
            $alias = array_keys($table)[0];
            $table = $table[$alias];
        }

        // sprawdzam czy alias jest juz zajety
        if (isset($this->joins[$alias])) {
            // alias jest juz zajety wiec generuje nowy
            $alias = $this->newAlias();
        }

        if (!is_array($condition)) {
            // warunek nie jest arrayem wiec testuje go pod kotem prostego
            // stringa
            if ($this->isSimpleString($condition)) {
                // zostala podana nazwa kolumny laczacej wiec zamieniam to na
                // arraya aby wykorzystac mechanizm laczenia
                $condition = array($condition);
            }
        }

        if (is_array($condition)) {
            // warunek zostal podany jako array wiec odwoluje sie do ostatniej
            // dolaczonej tabeli
            if (empty($this->lastJoinedTable)) {
                $lastJoinedTable = $this->alias();
            }else{
                $lastJoinedTable = $this->lastJoinedTable[count($this->lastJoinedTable)-1];
            }

            // tworze zmienna na warunek
            $conditionString = "";

            foreach ($condition as $index => $column) {
                if (is_int($index)) {
                    // jesli mamy podana tylko kolumne, to zakladam ze to jest
                    // taka sama kolumna w obu tabelach
                    $conditionString .= "$alias.$column = $lastJoinedTable.$column AND ";
                }else{
                    // jest podany kolumna tabeli pierwszej oraz tabeli trugie
                    $conditionString .= "$alias.$index = $lastJoinedTable.$column AND ";
                }
            }

            $conditionString = rtrim($conditionString, ' AND ');
            $condition = $conditionString;
        }

        foreach ($columns as $index => $value) {
            if (is_int($index)) {
                // nie mamy zdefiniowanego aliasu wiec sprawdzam czy wartosc
                // jest prosta, jak jest prosta to dodaje przedrostek
                // joinowanej tabeli
                if ($this->isSimpleString($value)) {
                    // dodaje przedrostek joinowanej tabeli
                    $this->column("$alias.$value");
                }else{
                    // jest to wartosc zlozona, wiec pewnie zostanie zwrocony
                    // wyjatek
                    $this->column($value);
                }
            }else{
                // zostal podany alias wiec to samo co wczesniej ale z aliasem
                if ($this->isSimpleString($value)) {
                    $this->column("$alias.$value", $index);
                }else{
                    $this->column($value, $index);
                }
            }
        }

        $assert->notHasIndex($this->joins, $alias, "The alias $alias is used. You must define other alias.");

        // save last joined table to use later if will be use array as
        // condition
        $this->lastJoinedTable[] = $alias;

        $this->joins[$alias] = array(
            'table' => $table,
            'condition' => $condition,
            'type' => $type,
        );

        return $this;
    }

    /**
     * Use this method if you want to change pointer UP to last joined table.
     * This has influence to join condition at join methods.
     */
    public function pointer($alias = null)
    {
        if (is_null($alias)) {
            if (!empty($this->lastJoinedTable)) {
                array_pop($this->lastJoinedTable);
            }

            return $this;
        }

        while(!empty($this->lastJoinedTable)){
            $last = array_pop($this->lastJoinedTable);
            if ($last == $alias) {
                $this->lastJoinedTable[] = $last;
                return $this;
            }
        }

        return $this;
    }

    private function isSimpleString($string)
    {
        $re = "/^[a-zA-Z,_,0-9]*$/";
        $match = preg_match($re, $string);

        if (!empty($match)) {
            return true;
        }

        return false;
    }

    public function joins()
    {
        return $this->joins;
    }

    public function sql()
    {
        $assert = $this->service('assert');

        $from = $this->from();
        $columns = $this->columns();
        $where = $this->where();
        $orders = $this->orders();
        $limit = $this->limit();

        $sql = "SELECT\n";

        if(count($columns) == 0){
            $assert->exception("You must define at least one column.");
        }

        foreach ($columns as $alias => $columnDefinition) {
            $value = $columnDefinition['value'];
            $sql .= "($value) as $alias,\n";
        }

        $sql = rtrim($sql, ",\n");
        $sql .= "\n";

        // from
        if(is_null($from)){
            $assert->exception("You must define the from in SelectStatement.");
        }

        $alias = array_keys($from)[0];
        $table = $from[$alias];

        if ($alias == $table) {
            $sql .= "FROM `$table`\n";
        }else{
            $sql .= "FROM `$table` as $alias\n";
        }

        // joins
        $joins = $this->joins();
        foreach ($joins as $alias => $def) {
            $joinStatement = '';
            switch ($def['type']) {
            case self::JOIN_TYPE_INNER:
                $joinStatement = 'INNER JOIN';
                break;
            case self::JOIN_TYPE_LEFT:
                $joinStatement = 'LEFT JOIN';
                break;
            case self::JOIN_TYPE_RIGHT:
                $joinStatement = 'RIGHT JOIN';
                break;
            case self::JOIN_TYPE_OUTER:
                $joinStatement = 'FULL OUTER JOIN';
                break;
            default:
                $assert->exception("Wrong type of join.");
            }

            $table = $def['table'];
            $condition = $def['condition'];

            if ($table == $alias) {
                $sql .= "$joinStatement `$table` on $condition\n";
            }else{
                $sql .= "$joinStatement `$table` as $alias on $condition\n";
            }
        }

        // where
        if (!is_null($where)) {
            $sql .= "WHERE $where\n";
        }

        // orders
        if(count($orders) > 0){
            $sql .= 'ORDER BY ';
            foreach ($orders as $order) {
                $column = $order['column'];
                $type = $order['type'];

                $sql .= "$column $type, ";
            }

            $sql = rtrim($sql, ", ");
            $sql .= "\n";
        }

        // limit
        if (!is_null($limit)) {
            $offset = $limit['offset'];
            $limit = $limit['limit'];

            if($offset > 0){
                $sql .= "LIMIT $limit, $offset";
            }else{
                $sql .= "LIMIT $limit";
            }
        }

        // na dobra sprawe nie wiem na ktorym etapie zakonczylo sie budowanie
        // zapytania , jesli podano tylko FROM to na koncu zapytania bedzie
        // zalamanie lini, jesli podana wszystkie elementy do na zapytania nie
        // bedzie zalamania lini.
        $sql = rtrim($sql, "\n");

        $sql = $this->processSql($sql);

        return $sql;
    }

    /**
     * Add column do select statement.
     *
     * @param string|array @value It can be
     * @param string @alias
     * @return Jub\ORM\Statement\Conditional\Select;
     *
     * @example "
     * $select->column('author_id');
     * // ...
     * // authors.author_id as author_id
     *
     * $select->column('author_id', 'idOfAuthor);
     * // ...
     * // authors.author_id as idOfAuthor
     *
     * $select->column('books.author_id', 'idOfAuthor);
     * // ...
     * // books.author_id as idOfAuthor
     *
     * $select->column(array('author_id', 'first_name));
     * // ...
     * // authors.author_id as author_id
     * // authors.first_name as first_name
     *
     * $select->column(array(
     *  'author_id' => 'idOfAuthor',
     *  'first_name => 'firstName',
     * ));
     * // ...
     * // authors.author_id as idOfAuthor
     * // authors.first_name as firstName
     * "
     */
    public function column($value, $alias = null)
    {
        if (is_array($value)) {
            foreach ($value as $index => $value) {
                if (is_int($index)) {
                    $this->column($value);
                }else{
                    $this->column($value, $index);
                }
            }

            return $this;
        }

        $assert = $this->service('assert');

        if ($this->isSimpleString($value)) {
            // jest to klumna bezposrednio podana bez aliasu tabeli wiec
            // zakladam ze kolumna odnosi sie do tabeli bazowej
            if (is_null($alias)) {
                // dodatkowo zapisuje alias na wypadek jak by nie byl podany
                $alias = $value;
            }

            $value = $this->alias().'.'.$value;
        }else{
            //definicja kolumny jest zlozona
            if (is_null($alias)) {
                // i nie podano nam aliasu wiec prubuje jeszcze raz wyciagnac
                // alias za pomoca wyrazenia regularnego
                $re = "/^[a-zA-Z,_,0-9]*\\.([a-zA-Z,_,0-9]*)$/";
                preg_match($re, $value, $matches);

                if (!empty($matches)) {
                    // udalo sie wyciagnac alias np. books.author_id wiec
                    // aliasem bedzie author_id
                    $alias = $matches[1];
                }
            }
        }

        if (is_null($alias)) {
            // nie udalo sie pobrac aliasu, a kazda kolumna powinna miec alias
            // wiec wyrzucam wyjatek
            $assert->exception("You must define alias to $value.");
        }

        $this->columns[$alias] = array(
            'value' => $value,
        );

        return $this;
    }

    /**
     * Return columns of select.
     *
     * @return array All select column (alias => value)
     */
    public function columns()
    {
        return $this->columns;
    }

    public function limit($limit = null , $offset = 0)
    {
        if (is_null($limit)) {
            return $this->limit;
        }

        $this->limit = array(
            'limit' => $limit,
            'offset' => $offset
        );

        return $this;
    }

    /**
     * Add order to select.
     *
     * @param string @column
     * @param string @type ASC|DESC
     * @return self
     */
    public function order($column = null, $type = self::ORDER_ASC)
    {
        $this->orders[] = array(
            'column' => $column,
            'type' => $type
        );

        return $this;
    }

    /**
     * Return select orders
     *
     * @return array Array with all order of Select
     */
    public function orders()
    {
        return $this->orders;
    }
}
