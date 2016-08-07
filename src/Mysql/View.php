<?php
namespace Midata\Mysql;

use Midata\Object\View as MidataView;

class View extends MidataView
{
    private $data;

    public function definition()
    {
        $assert = $this->service('assert');

        $adapter = $this->adapter();
        $dbname = $adapter->config('dbname');
        $viewName = $this->name();

        $sql = "SHOW CREATE VIEW $viewName";

        $result = $this->execute($sql);

        if (empty($result)) {
            $assert->exception("There are no metadata about $viewName view.");
        }

        $create = $result[0]['Create View'];

        $re = "/.*?AS (.*)/";

        if(preg_match($re, $create, $matches) === false){
            $assert->exception("Can't extract view body from create view $viewName");
        }

        return $matches[1];
    }
}
