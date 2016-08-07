<?php
namespace Midata\DML;

use Midata\Midata;

class Brackets extends Midata
{
    protected $elements = array();
    protected $operator;

    function __construct()
    {
        $this->andOperator();
    }

    public function add($element)
    {
        if (count($this->elements) > 0) {
            $this->elements[] = $this->operator;
        }

        $this->elements[] = $element;

        return $this;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function andOperator()
    {
        $this->operator = 'AND';
        return $this;
    }

    public function orOperator()
    {
        $this->operator = 'OR';
        return $this;
    }

}
