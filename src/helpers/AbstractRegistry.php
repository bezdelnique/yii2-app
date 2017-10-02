<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 29.06.17
 * Time: 21:04
 */

namespace bezdelnique\yii2app\helpers;


class AbstractRegistry
{
    /**
     * Class instances. Must be redefine in Child class.
     * @var array
     */
    static private $_instances = [];


    private function __construct()
    {
    }


    protected static function set($key, $instance)
    {
        if (isset(static::$_instances[$key]) == true) {
            throw new ExceptionHelper('Instance already defined in namespace: key: ' . $key . '. Please use exists() method.');
        }

        static::$_instances[$key] = $instance;
        return static::$_instances[$key];
    }


    protected static function get($key)
    {
        if (isset(static::$_instances[$key]) == false) {
            throw new ExceptionHelper('Instance does not defined in namespace: key: ' . $key . '. Please use exists() method.');
        }

        return static::$_instances[$key];
    }


    protected static function exists($key)
    {
        if (isset(static::$_instances[$key]) == true) {
            return true;
        }

        return false;
    }
}

