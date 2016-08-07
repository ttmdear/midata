<?php
namespace Midata\Tests;

use Midata\Adapter;
use \Exception;

class Base extends \PHPUnit_Framework_TestCase
{
    protected function md5($var)
    {
        return md5(var_export($var, true));
    }

    protected function inline($var)
    {
        $var = explode("\n", var_export($var,true));
        $inline = "";

        foreach ($var as $element) {
            $inline .= $element;
        }

        $inline = preg_replace('/\s+/', '', $inline);

        return $inline;
    }

    protected function init()
    {
    }

    public function adapter()
    {
        return Adapter::factory('mysql', array(
            'adapter' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'bookstore_midata',
            'username' => 'root',
            'password' => '',
        ), 'bookstore');
    }

    protected function table($name)
    {
        $adapter = $this->adapter();
        return $adapter->table($name);
    }

    protected function view($name)
    {
        $adapter = $this->adapter();
        return $adapter->view($name);
    }
}
