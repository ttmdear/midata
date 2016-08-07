<?php
namespace Midata;

use Midata\Assert;
use Midata\Util;

class Midata
{
    private static $services;

    public static function service($name)
    {
        if (!isset(self::$services[$name])) {
            self::initService($name);
        }

        return self::$services[$name];
    }

    private static function initService($service)
    {
        switch ($service) {
        case 'assert':
            self::$services[$service] = new Assert();
            return;
        case 'util':
            self::$services[$service] = new Util();
            return;
        default:
            throw new Exception("There is no service $service");
            break;
        }
    }
}
