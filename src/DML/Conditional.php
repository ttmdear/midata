<?php
namespace Midata\DML;

use Midata\DML\Brackets as DMLBrackets;
use Midata\DML as MidataDML;

abstract class Conditional extends MidataDML
{
    private $bracketsStack = array();
    private $brackets;
    private $mainBrackets;

    function __construct()
    {
        $this->initBrackets();
    }

    protected function initBrackets()
    {
        $brackets = new DMLBrackets();

        $this->mainBrackets = $brackets;
        $this->brackets = $brackets;
        //$this->bracketsStack[] = $brackets;
    }

    public function brackets($function, $scope = null)
    {
        $assert = $this->service('assert');
        $assert->isCallable($function, 'The parameter to brackets should be callback.');

        if (is_null($scope)) {
            $scope = $this;
        }

        $this->openBrackets();
        call_user_func_array($function, array($scope));
        $this->closeBrackets();

        return $this;
    }

    private function openBrackets()
    {
        // zapisuje poprzedni nawias
        $this->bracketsStack[] = $this->brackets;

        // tworze nowy nazwias i dodaje jako element do starego
        $brackets = new DMLBrackets();
        $this->brackets->add($brackets);

        $this->brackets = $brackets;
    }

    private function closeBrackets()
    {
        $assert = $this->service('assert');
        $last = array_pop($this->bracketsStack);

        $assert->notNull($last, 'Logic error.');

        $this->brackets = $last;
    }

    // (wA AND wB)
    public function andOperator()
    {
        $this->brackets->andOperator();
        return $this;
    }

    public function orOperator()
    {
        $this->brackets->orOperator();
        return $this;
    }

    // methods
    public function equal($column, $to)
    {
        $uniqueId = $this->uniqueId();

        $where = "$column = :$uniqueId:";
        $this->brackets->add($where);
        $this->bind($uniqueId, $to);

        return $this;
    }

    public function in($column, $in)
    {
        $uniqueId = $this->uniqueId();

        $where = "{in $column $uniqueId}";
        $this->brackets->add($where);
        $this->bind($uniqueId, $in);

        return $this;
    }

    public function like($column, $like)
    {
        $uniqueId = $this->uniqueId();

        $where = "$column like :$uniqueId:";
        $this->brackets->add($where);
        $this->bind($uniqueId, $like);

        return $this;
    }

    public function isNull($column)
    {
        $where = "$column is null";
        $this->brackets->add($where);
        return $this;
    }

    public function isNotNull($column)
    {
        $where = "$column is not null";
        $this->brackets->add($where);
        return $this;
    }

    public function startWith($column, $like)
    {
        return $this->like($column, "$like%");
    }

    public function endWith($column, $like)
    {
        return $this->like($column, "%$like");
    }

    public function contains($column, $like)
    {
        return $this->like($column, "%$like%");
    }

    public function expr($expr)
    {
        $where = "$expr";
        $this->brackets->add($where);
        return $this;
    }

    /**
     * Return where string for statement. String contains placeholders of
     * binds, so you should getBind to proper read where.
     *
     * @return string
     */
    public function where()
    {
        $where = "";
        $this->travers($this->mainBrackets->getElements(), $where);

        if (empty($where)) {
            return null;
        }

        return $where;
    }

    protected function travers($elements, &$where)
    {
        foreach ($elements as $element) {
            if ($element instanceof DMLBrackets) {
                $where .= '(';
                $this->travers($element->getElements(), $where);

                $where = rtrim($where);
                $where .= ') ';
            }else{
                $where .= "$element ";
            }
        }
    }
}
