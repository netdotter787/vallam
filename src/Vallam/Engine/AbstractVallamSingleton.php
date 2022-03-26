<?php


namespace Vallam\Engine;


use Exception;

abstract class AbstractVallamSingleton
{
    private static $instances = [];

    public static function getInstance()
    {
        $cls = static::class;

        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    protected function __construct()
    {
        $this->__boot();
    }

    protected function __clone() {}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot be restored.");
    }

    protected function __boot() {}

    public static function createInstance()
    {
        return self::getInstance();
    }
}