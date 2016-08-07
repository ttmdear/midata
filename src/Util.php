<?php
namespace Midata;

use Midata\Midata;

class Util extends Midata
{
    public function arrayHas($array, $index)
    {
        $assert = $this->service('assert');
        $assert->isArray($array);

        return in_array($index, array_keys($array));
    }
}
